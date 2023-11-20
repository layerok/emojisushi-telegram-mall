<?php

namespace Layerok\Tgmall\Features\Product;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\Tgmall\Features\Category\CategoryFooterKeyboard;
use Layerok\Tgmall\Features\Category\CategoryProductKeyboard;
use Layerok\TgMall\Objects\Product;
use Layerok\TgMall\Objects\Variant;


class AddProductHandler extends Handler
{
    protected string $name = "product_add";

    protected Product $product;

    protected ?Variant $variant;

    public function run()
    {
        Validator::validate($this->arguments, [
            'product_id' => 'required_without:variant_id|exists:offline_mall_products,id',
            'variant_id' => 'required_without:product_id|exists:offline_mall_product_variants,id',
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
