<?php namespace Layerok\Tgmall\Features\Category;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Classes\Traits\CallbackData;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Facades\EmojisushiApi;

class CategoryProductKeyboard extends InlineKeyboard
{
    use Lang;
    use CallbackData;

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
            'text' => self::lang('buttons.price') . ": " . $entry['prices'][0]['price_formatted'],
            'callback_data' => self::prepareCallbackData('noop')
        ]);

        if(isset($entry['product_id'])) {
            $this->append([
                'text' => $entry['description'],
                'callback_data' => self::prepareCallbackData('noop')
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
                'text' => self::lang('buttons.added_to_cart'),
                'callback_data' => self::prepareCallbackData('noop')
            ]);
        } else {
           $this->append([
                'text' => self::lang('buttons.add_to_cart'),
                'callback_data' => self::prepareCallbackData(
                    'product_add',
                    array_merge(
                        ['qty' =>  1],
                        isset($entry['product_id']) ?
                            ['variant_id' => $entry['id']] :
                            ['product_id' => $entry['id']]
                    )
                )
            ]);
        }


        $this->nextRow();
    }

}
