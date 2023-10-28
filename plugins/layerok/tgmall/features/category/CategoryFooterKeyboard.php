<?php namespace Layerok\Tgmall\Features\Category;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Facades\EmojisushiApi;
use Config;
use Layerok\TgMall\Objects\Cart;

class CategoryFooterKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        $limit = Config::get('layerok.tgmall::settings.products.per_page', 10);

        // todo: handle 404 error
        $category = EmojisushiApi::getCategory(['id' => $this->vars['category_id']]);

        $products = EmojisushiApi::getProducts([
            'category_slug' => $category->slug,
        ])->data;

        $lastPage = ceil(count($products) / max(1, $limit));

        if ($lastPage > $this->vars['page']) {
            $this->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.load_more'),
                'callback_data' => json_encode([
                    'category_item',
                    [
                        'id' => $category->id,
                        'page' => $this->vars['page'] + 1
                    ]
                ])
            ])->nextRow();
        }

        /**
         * @var Cart $cart;
         */
        $cart = $this->vars['cart'];

        $this
            ->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.cart') .
                    (count($cart->data) ? sprintf("(%s)", count($cart->data)) : ''),
                'callback_data' => json_encode([
                    'cart', ['type' => 'list']
                ])
            ])
            ->nextRow()
            ->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.to_categories'),
                'callback_data' => json_encode([
                    'category_items', []
                ])
            ])
            ->nextRow()
            ->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.in_menu_main'),
                'callback_data' => json_encode([
                    'start',
                    []
                ])
            ]);

    }
}
