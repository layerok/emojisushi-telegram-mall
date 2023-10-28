<?php namespace Layerok\Tgmall\Features\Category;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Objects\Product;
use Layerok\TgMall\Objects\Variant;

class CategoryProductKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        /** @var Product $product */
        $product = $this->vars['product'];

        if($product->inventory_management_method === 'variant') {
            $variants = array_filter($product->variants, function(Variant $variant) {
                return $variant->published;
            });

            collect($variants)->each(function(Variant $variant) {
                $this->makeVariantRow($variant);
            });

        } else {
            $this->makeProductRow($product);
        }
    }

    public function makeProductRow(Product $product)
    {
        $this->append([
            'text' => sprintf(
                "%s: %s",
                \Lang::get('layerok.tgmall::lang.telegram.buttons.price'),
                $product->prices[0]->price_formatted
            ),
            'callback_data' => json_encode(['noop', []])
        ]);

        $cartProduct = EmojisushiApi::getCartProduct([
            'product_id' => $product->id,
        ]);

        if (!!$cartProduct) {
            $this->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.added_to_cart'),
                'callback_data' => json_encode(['noop', []])
            ]);
        } else {
           $this->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.add_to_cart'),
                'callback_data' => json_encode([
                    'product_add',
                    ['qty' =>  1, 'product_id' => $product->id]
                ])
            ]);
        }


        $this->nextRow();
    }

    public function makeVariantRow(Variant $variant)
    {
        $this->append([
            'text' => sprintf(
                "%s: %s",
                \Lang::get('layerok.tgmall::lang.telegram.buttons.price'),
                $variant->prices[0]->price_formatted
            ),
            'callback_data' => json_encode(['noop', []])
        ]);

        $this->append([
            'text' => $variant->description,
            'callback_data' => json_encode(['noop', []])
        ]);

        $cartProduct = EmojisushiApi::getCartProduct([
            'product_id' => $variant->product->id,
            'variant_id' => $variant->id
        ]);

        if (!!$cartProduct) {
            $this->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.added_to_cart'),
                'callback_data' => json_encode(['noop', []])
            ]);
        } else {
            $this->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.add_to_cart'),
                'callback_data' => json_encode([
                    'product_add',
                    [
                        'qty' =>  1,
                        'variant_id' => $variant->id
                    ]
                ])
            ]);
        }


        $this->nextRow();
    }

}
