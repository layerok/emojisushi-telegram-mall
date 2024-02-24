<?php

namespace App\Features\Product;

use App\Classes\Callbacks\Handler;
use App\Facades\EmojisushiApi;
use App\Features\Category\CategoryFooterKeyboard;
use App\Features\Category\CategoryProductKeyboard;
use App\Objects\Product;
use App\Objects\Variant;
use Illuminate\Support\Facades\Validator;


class AddProductHandler extends Handler
{
    protected string $name = "product_add";

    protected Product $product;

    protected ?Variant $variant;

    public function run()
    {
        Validator::validate($this->arguments, [
            'qty' => 'required|integer|min:1'
        ]);

        if (isset($this->arguments['product_id'])) {
            $this->variant = null;
            $this->product = EmojisushiApi::getProduct([
                'product_id' => $this->arguments['product_id']
            ]);
        } else if (isset($this->arguments['variant_id'])) {
            $this->variant = EmojisushiApi::getVariant([
                'variant_id' => $this->arguments['variant_id']
            ]);
            $this->product = EmojisushiApi::getProduct([
                'product_id' => $this->variant['product_id']
            ]);
        }

        $cart = EmojisushiApi::addCartProduct(
            array_merge([
                'product_id' => $this->product->id,
                'quantity' => $this->arguments['qty'],
            ], $this->variant ? [
                'variant_id' => $this->variant->id
            ]: [])
        );


        $cartCountMsg = $this->user->state->cart_count_msg;

        $this->api->editMessageReplyMarkup([
            'message_id' => $this->getUpdate()->getMessage()->message_id,
            'chat_id' => $this->getUpdate()->getChat()->id,
            'reply_markup' => (new CategoryProductKeyboard($this->product))->getKeyboard()
        ]);

        if (!$cartCountMsg) {
            return;
        }

        if ($cartCountMsg->count == $cart->totalQuantity) {
            // Кол-во товаров в корзине совпадает с тем, что написано в сообщении
            return;
        }

        $this->api->editMessageReplyMarkup([
            'message_id' => $cartCountMsg->id,
            'chat_id' => $this->getUpdate()->getChat()->id,
            'reply_markup' => (new CategoryFooterKeyboard(
                $cartCountMsg->category_id,
                $cartCountMsg->page,
                $cart)
            )->getKeyboard()
        ]);
    }


}
