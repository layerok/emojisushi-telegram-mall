<?php namespace Layerok\Tgmall\Features\Cart;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;
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

    public function updateProduct()
    {
        $cart = EmojisushiApi::getCart();

        $cartProduct = collect($cart['data'])
            ->where('id', '=', $this->arguments['id'])
            ->first();

        if (isset($cartProduct) && ($cartProduct['quantity'] + $this->arguments['qty']) < 1) {
            return;
        }

        $cart = EmojisushiApi::addCartProduct(
            array_merge([
                'product_id'=> $cartProduct['product']['id'],
                'quantity' => $this->arguments['qty']
            ], $cartProduct['variant'] ? [
                'variant_id' => $cartProduct['variant']['id'],
            ]: [])
        );

        $cartProduct = collect($cart['data'])
            ->where('id', '=', $this->arguments['id'])
            ->first();

        $this->editCartProductMessage($cartProduct);

        $this->editCartFooterMessage($cart);

    }

    public function removeProduct()
    {
        $this->api->deleteMessage([
            'chat_id' => $this->getUpdate()->getChat()->id,
            'message_id' => $this->getUpdate()->getMessage()->message_id
        ]);

        $cart = EmojisushiApi::getCart();

        $cartProduct = collect($cart['data'])
            ->where('id', '=', $this->arguments['id'])
            ->first();

        if(!isset($cartProduct)) {
            return;
        }

        $cart = EmojisushiApi::addCartProduct([
            'product_id' => $cartProduct['product']['id'],
            'quantity' => $cartProduct['quantity'] * -1
        ]);

        $this->editCartFooterMessage($cart);
    }


    public function showCart()
    {
        $cart = EmojisushiApi::getCart();

        $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.cart')
        ]);

        collect($cart['data'])->map(function ($cartProduct) {
            $this->sendCartProduct($cartProduct);
        });

        $response = $this->replyWithMessage(
            $this->cartFooterMessage($cart)
        );

        if (count($cart['data']) === 0) {
            return;
        }

        $msg_id = $response["message_id"];

        $this->getUser()->state->setStateValue('cart_total_msg',
            [
                'id' => $msg_id,
                'total' => $cart['total']
            ]
        );
    }

    public function sendCartProduct(array $cartProduct)
    {
        $caption = sprintf(
            "<b>%s</b>\n\n%s",
                $cartProduct['product']['name'],
                \Html::strip($cartProduct['product']['description_short'])
            );

        $markup = new CartProductKeyboard([
            'cartProduct' => $cartProduct,
        ]);

        $keyboard = $markup->getKeyboard();

        if(\Cache::has("telegram.files." . $cartProduct['product']['id'])) {
            $response = $this->replyWithPhoto([
                'photo' => \Cache::get("telegram.files." . $cartProduct['product']['id']),
                'caption' => $caption,
                'reply_markup' => $keyboard,
                'parse_mode' => 'html',
            ]);

            \Cache::set(
                "telegram.files." . $cartProduct['product']['id'],
                $response->getPhoto()->last()['file_id']
            );
        } else if(isset($cartProduct['product']['image_sets'][0]['images'][0]['path'])) {
            $this->replyWithPhoto([
                'photo' => InputFile::create($cartProduct['product']['image_sets'][0]['images'][0]['path']),
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

    public function editCartProductMessage($cartProduct)
    {
        $markup = new CartProductKeyboard([
            'cartProduct' => $cartProduct,
        ]);

        $this->api->editMessageReplyMarkup([
            'message_id' => $this->getUpdate()->getMessage()->message_id,
            'chat_id' => $this->getUpdate()->getChat()->id,
            'reply_markup' => $markup->getKeyboard()
        ]);
    }

    public function editCartFooterMessage($cart)
    {
        $cartTotalMsg = $this->getUser()->state->getStateValue('cart_total_msg');

        if (!isset($cartTotalMsg)) {
            return;
        }

        if ($cartTotalMsg['total'] == $cart['total']) {
            // Общая стоимость товаров в корзине совпадает с тем что написано в сообщении
            return;
        }
        $this->api->editMessageReplyMarkup(array_merge([
            'message_id' => $cartTotalMsg['id'],
            'chat_id' => $this->getUpdate()->getChat()->id,
        ], $this->cartFooterMessage($cart)));

        $this->getUser()->state->setStateValue('cart_total_msg',
            array_merge(
                $this->state['cart_total_msg'] ?? [],
                ['total' => $cart['total']]
            )
        );
    }


    public function cartFooterMessage($cart): array
    {
        $text = count($cart['data']) === 0 ?
            \Lang::get('layerok.tgmall::lang.telegram.texts.cart_is_empty') :
            \Lang::get('layerok.tgmall::lang.telegram.texts.cart');
        return [
            'text' => $text,
            'reply_markup' => $this->cartFooterKeyboard($cart)
        ];
    }

    public function cartFooterKeyboard($cart): Keyboard
    {
        if (count($cart['data']) === 0) {
            $markup = new CartEmptyKeyboard();
        } else {
            $markup = new CartFooterKeyboard([
                'cart' => $cart
            ]);
        }

        return $markup->getKeyboard();
    }


}
