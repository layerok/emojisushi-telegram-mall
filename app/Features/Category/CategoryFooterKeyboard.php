<?php namespace App\Features\Category;

use App\Facades\EmojisushiApi;
use App\Objects\Cart;
use Config;
use Telegram\Bot\Keyboard\Keyboard;

class CategoryFooterKeyboard
{
    public function __construct(public $category_id, public $page, public Cart $cart)
    {

    }

    public function getKeyboard(): Keyboard
    {
        $keyboard = (new Keyboard())->inline();

        $limit = Config::get('settings.products.per_page', 10);

        // todo: handle 404 error
        $category = EmojisushiApi::getCategory(['id' => $this->category_id]);

        $products = EmojisushiApi::getProducts([
            'category_slug' => $category->slug,
        ])->data;

        $lastPage = ceil(count($products) / max(1, $limit));

        if ($lastPage > $this->page) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => \Lang::get('lang.telegram.buttons.load_more'),
                    'callback_data' => json_encode([
                        'category_item',
                        [
                            'id' => $category->id,
                            'page' => $this->page + 1
                        ]
                    ])
                ])
            ])->row([]);
        }


        return $keyboard
            ->row([
                Keyboard::inlineButton([
                    'text' => \Lang::get('lang.telegram.buttons.cart') .
                        (count($this->cart->data) ? sprintf("(%s)", count($this->cart->data)) : ''),
                    'callback_data' => json_encode([
                        'cart', ['type' => 'list']
                    ])
                ])
            ])
            ->row([])
            ->row([
                Keyboard::inlineButton([
                    'text' => \Lang::get('lang.telegram.buttons.to_categories'),
                    'callback_data' => json_encode([
                        'category_items', []
                    ])
                ])
            ])
            ->row([])
            ->row([
                Keyboard::inlineButton([
                    'text' => \Lang::get('lang.telegram.buttons.in_menu_main'),
                    'callback_data' => json_encode([
                        'start',
                        []
                    ])
                ])
            ]);
    }
}
