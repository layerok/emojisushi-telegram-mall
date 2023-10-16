<?php namespace Layerok\Tgmall\Features\Category;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Classes\Traits\CallbackData;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Facades\EmojisushiApi;
use Config;

class CategoryFooterKeyboard extends InlineKeyboard
{
    use Lang;
    use CallbackData;

    public function build(): void
    {
        $limit = Config::get('layerok.tgmall::settings.products.per_page', 10);

        $categories = EmojisushiApi::getCategories()['data'];
        $category = array_filter($categories, function($c) {
            return $c['id'] === $this->vars['category_id'];
        })[0];

        $products = EmojisushiApi::getProducts([
            'category_slug' => $category['slug'],
        ])['data'];


        $lastPage = ceil(count($products) / max(1, $limit));


        if ($lastPage > $this->vars['page']) {
            $this->append([
                'text' => self::lang('buttons.load_more'),
                'callback_data' => self::prepareCallbackData(
                    'category_item',
                    [
                        'id' => $category['id'],
                        'page' => $this->vars['page'] + 1
                    ]
                )
            ])->nextRow();
        }

        $cart = EmojisushiApi::getCart();

        $this
            ->append([
                'text' => self::lang('buttons.cart') .
                    (count($cart['data']) ? sprintf("(%s)", count($cart['data'])) : ''),
                'callback_data' => self::prepareCallbackData(
                    'cart', ['type' => 'list']
                )
            ])
            ->nextRow()
            ->append([
                'text' => self::lang('buttons.to_categories'),
                'callback_data' => self::prepareCallbackData(
                    'category_items'
                )
            ])
            ->nextRow()
            ->append([
                'text' => self::lang('buttons.in_menu_main'),
                'callback_data' => self::prepareCallbackData(
                    'start',
                    []
                )
            ]);

    }
}
