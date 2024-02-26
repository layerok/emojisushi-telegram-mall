<?php

namespace App\Features\Cart;

use App\Classes\Callbacks\Handler;
use App\Classes\Html;
use App\Facades\EmojisushiApi;
use App\Facades\Hydrator;
use App\Objects\Cart;
use App\Objects\CartProduct;
use App\Objects2\CartTotalMsg;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;

class CartHandler extends Handler
{
    protected string $name = "cart";

    public array $types = ['remove', 'update', 'list'];

    public function run()
    {
        Validator::validate($this->arguments, [
            'type' => [
                'required',
                Rule::in($this->types),
            ],
            'qty' => 'integer'
        ]);

        $type = $this->arguments['type'];

        switch ($type) {
            case "list":
                $this->showCart();
                break;
            case "update":
                $this->updateProduct();
                break;
            case "remove":
                $this->removeProduct();
                break;
        }
    }

    public function eraseCartProductMessage()
    {
        $message = $this->getUpdate()->getMessage();
        if($message->getCaption()) {
            $this->api->editMessageCaption([
                'message_id' => $this->getUpdate()->getMessage()->message_id,
                'chat_id' => $this->getUpdate()->getChat()->id,
                'caption' => sprintf(
                    \Lang::get('lang.telegram.texts.product_no_longer_exists'),
                    $this->getCartProductTitleFromCaption(
                        $message->getCaption()
                    )
                )
            ]);
        } else {
            $this->api->editMessageText([
                'message_id' => $this->getUpdate()->getMessage()->message_id,
                'chat_id' => $this->getUpdate()->getChat()->id,
                'text' => sprintf(
                    \Lang::get('lang.telegram.texts.product_no_longer_exists'),
                    $message->getText()
                )
            ]);
        }
    }

    public function updateProduct()
    {
        $cart = EmojisushiApi::getCart();

        /**
         * @var CartProduct $cartProduct
         */
        $cartProduct = collect($cart->data)->first(function (CartProduct $cartProduct) {
            return $cartProduct->id === $this->arguments['id'];
        });

        $cartProductExists = $this->checkCartProduct($cart, $cartProduct);

        if (!$cartProductExists) {
            return;
        }

        if (isset($cartProduct) && ($cartProduct->quantity + $this->arguments['qty']) < 1) {
            return;
        }

        $cart = EmojisushiApi::addCartProduct(
            array_merge([
                'product_id' => $cartProduct->product->id,
                'quantity' => $this->arguments['qty']
            ], $cartProduct->variant ? [
                'variant_id' => $cartProduct->variant->id,
            ] : [])
        );

        $cartProduct = collect($cart->data)->first(function (CartProduct $cartProduct) {
            return $cartProduct->id === $this->arguments['id'];
        });

        $this->api->editMessageReplyMarkup([
            'message_id' => $this->getUpdate()->getMessage()->message_id,
            'chat_id' => $this->getUpdate()->getChat()->id,
            'reply_markup' => (new CartProductKeyboard($cartProduct))->getKeyboard()
        ]);

        $this->editCartFooterMessage($cart);

    }

    public function getCartProductTitleFromCaption(string $caption)
    {
        $matches = [];
        preg_match('/.*/', $caption, $matches);
        return $matches[0];
    }

    public function removeProduct()
    {
        $cart = EmojisushiApi::getCart();

        /** @var CartProduct $cartProduct */
        $cartProduct = collect($cart->data)->first(function (CartProduct $cartProduct) {
            return $this->arguments['id'] === $cartProduct->id;
        });

        $cartProductExists = $this->checkCartProduct($cart, $cartProduct);

        if (!$cartProductExists) {
            return;
        }

        $cart = EmojisushiApi::removeFromCart([
            'cart_product_id' => $cartProduct->id
        ]);

        $this->api->deleteMessage([
            'chat_id' => $this->getUpdate()->getChat()->id,
            'message_id' => $this->getUpdate()->getMessage()->message_id
        ]);

        $this->editCartFooterMessage($cart);
    }

    public function checkCartProduct(Cart $cart, ?CartProduct $cartProduct)
    {
        if (!isset($cartProduct)) {
            $this->eraseCartProductMessage();
            $this->editCartFooterMessage($cart);
            return false;
        }

        if (!$cartProduct->product->published) {
            $cart = EmojisushiApi::removeFromCart([
                'cart_product_id' => $cartProduct->id
            ]);
            $this->eraseCartProductMessage();
            $this->editCartFooterMessage($cart);
            return false;
        }
        return true;
    }


    public function showCart()
    {
        $cart = EmojisushiApi::getCart();

        $this->replyWithMessage([
            'text' => \Lang::get('lang.telegram.buttons.cart')
        ]);

        collect($cart->data)->map(function ($cartProduct) {
            $this->sendCartProduct($cartProduct);
        });

        $response = $this->replyWithMessage(
            $this->cartFooterMessage($cart)
        );

        if (count($cart->data) === 0) {
            return;
        }

        $cartTotalMsg = Hydrator::hydrate(CartTotalMsg::class, [
            'id' => $response->messageId,
            'total' => $cart->total,
        ]);

        $this->user->state->cart_total_msg = $cartTotalMsg;
        $this->user->save();

    }

    public function sendCartProduct(CartProduct $cartProduct)
    {
        $caption = sprintf(
            "<b>%s</b>\n\n%s",
            $cartProduct->product->name,
            Html::strip($cartProduct->product->description_short)
        );

        $markup = new CartProductKeyboard($cartProduct);

        $keyboard = $markup->getKeyboard();

        if (\Cache::has("telegram.files." . $cartProduct->product->id)) {
            $response = $this->replyWithPhoto([
                'photo' => \Cache::get("telegram.files." . $cartProduct->product->id),
                'caption' => $caption,
                'reply_markup' => $keyboard,
                'parse_mode' => 'html',
            ]);

            \Cache::set(
                "telegram.files." . $cartProduct->product->id,
                $response->getPhoto()->last()['file_id']
            );
        } else if (isset($cartProduct->product->image_sets[0]->images[0]->path)) {
            $this->replyWithPhoto([
                'photo' => InputFile::create($cartProduct->product->image_sets[0]->images[0]->path),
                'caption' => $caption,
                'reply_markup' => $keyboard,
                'parse_mode' => 'html',
            ]);
        } else {
            $this->replyWithMessage([
                'text' => $caption,
                'reply_markup' => $keyboard,
                'parse_mode' => 'html',
            ]);
        }
    }

    public function editCartFooterMessage(Cart $cart)
    {
        $cartTotalMsg = $this->user->state->cart_total_msg;

        if (!isset($cartTotalMsg)) {
            return;
        }

        if ($cartTotalMsg->total == $cart->total) {
            // Общая стоимость товаров в корзине совпадает с тем что написано в сообщении
            return;
        }
        $this->api->editMessageReplyMarkup(array_merge([
            'message_id' => $cartTotalMsg->id,
            'chat_id' => $this->getUpdate()->getChat()->id,
        ], $this->cartFooterMessage($cart)));


        $this->user->state->cart_total_msg->total = $cart->total;
        $this->user->save();
    }


    public function cartFooterMessage(Cart $cart): array
    {
        $text = count($cart->data) === 0 ?
            \Lang::get('lang.telegram.texts.cart_is_empty') :
            \Lang::get('lang.telegram.texts.cart');
        return [
            'text' => $text,
            'reply_markup' => $this->cartFooterKeyboard($cart)
        ];
    }

    public function cartFooterKeyboard(Cart $cart): Keyboard
    {
        if (count($cart->data) === 0) {
            $markup = new CartEmptyKeyboard();
        } else {
            $markup = new CartFooterKeyboard($cart);
        }

        return $markup->getKeyboard();
    }


}
