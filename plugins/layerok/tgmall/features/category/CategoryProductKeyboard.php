<?php namespace Layerok\Tgmall\Features\Category;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Facades\EmojisushiApi;

class CategoryProductKeyboard extends InlineKeyboard
{
    protected array $product;

    public function build(): void
    {
        $this->product = $this->vars['product'];

        if($this->product['inventory_management_method'] === 'variant') {
            $variants = array_filter($this->product['variants'], function($variant) {
                return $variant['published'];
            });

            array_map(function($variant) {
                $this->makeButtonsRow($variant);
            }, $variants);

        } else {
            $this->makeButtonsRow($this->product);
        }
    }


    public function makeButtonsRow($entry)
    {
        $this->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.price') . ": " . $entry['prices'][0]['price_formatted'],
            'callback_data' => json_encode(['noop', []])
        ]);

        if(isset($entry['product_id'])) {
            $this->append([
                'text' => $entry['description'],
                'callback_data' => json_encode(['noop', []])
            ]);
        }
        $variant = isset($entry['product_id']) ? $entry: null;
        $product = isset($entry['product_id']) ? $entry['product']: $entry;

        $inCart = EmojisushiApi::getCartProduct(array_merge([
            'product_id' => $product['id'],
        ], $variant ? [
            'variant_id' => $variant['id']
        ]: []));


        if (!!$inCart) {
            $this->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.added_to_cart'),
                'callback_data' => json_encode(['noop', []])
            ]);
        } else {
           $this->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.add_to_cart'),
                'callback_data' => json_encode([
                    'product_add',
                    array_merge(
                        ['qty' =>  1],
                        isset($entry['product_id']) ?
                            ['variant_id' => $entry['id']] :
                            ['product_id' => $entry['id']]
                    )
                ])
            ]);
        }


        $this->nextRow();
    }

}
