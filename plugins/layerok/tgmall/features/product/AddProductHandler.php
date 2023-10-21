<?php

namespace Layerok\Tgmall\Features\Product;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Traits\Warn;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\Tgmall\Features\Category\CategoryFooterKeyboard;
use Layerok\Tgmall\Features\Category\CategoryProductKeyboard;


class AddProductHandler extends Handler
{
    use Warn;

    protected string $name = "product_add";

    protected array $product;

    protected ?array $variant;

    public function validate(): bool
    {
        $rules = [
            'product_id' => 'required_without:variant_id|exists:offline_mall_products,id',
            'variant_id' => 'required_without:product_id|exists:offline_mall_product_variants,id',
            'qty' => 'required|integer|min:1'
        ];

        $validation = Validator::make($this->arguments, $rules);

        if ($validation->fails()) {
            $this->errors =
                array_merge(
                    $validation->errors()->get('product_id'),
                    $validation->errors()->get('variant_id'),
                    $validation->errors()->get('qty')
                );
            return false;
        }

        return true;

    }

    public function run()
    {
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
                'product_id' => $this->product['id'],
                'quantity' => $this->arguments['qty'],
            ], $this->variant ? [
                'variant_id' => $this->variant['id']
            ]: [])
        );


        $cartCountMsg = $this->getState()->getCartCountMsg();

        $markup = new CategoryProductKeyboard([
            'product' => $this->product,
        ]);

        if (!$cartCountMsg) {
            return;
        }

        $this->editMessageReplyMarkup(
            $this->getTriggerMessageId(),
            [
                'reply_markup' => $markup->getKeyboard()
            ]
        );

        if ($cartCountMsg['count'] == $cart['totalQuantity']) {
            // Кол-во товаров в корзине совпадает с тем, что написано в сообщении
            return;
        }

        $markup = new CategoryFooterKeyboard([
            'cart' => $cart,
            'category_id' => $cartCountMsg['category_id'],
            'page' => $cartCountMsg['page']
        ]);

        $this->editMessageReplyMarkup($cartCountMsg['id'], [
            'reply_markup' => $markup->getKeyboard()
        ]);


    }


}
