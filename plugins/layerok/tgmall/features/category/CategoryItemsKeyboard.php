<?php namespace Layerok\Tgmall\Features\Category;

use Layerok\TgMall\Objects\Category;
use Telegram\Bot\Keyboard\Keyboard;

class CategoryItemsKeyboard
{
    public array $vars;

    public function __construct($vars = [])
    {
        $this->vars = $vars;
    }

    public function getKeyboard(): Keyboard
    {
        $keyboard = (new Keyboard())->inline();
        collect($this->vars['categories'])->map(function (Category $category) use ($keyboard) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => $category->name,
                    'callback_data' => json_encode([
                        'category_item',
                        [
                            'id' => $category->id,
                            'page' => 1
                        ]
                    ])
                ])
            ])->row([]);
        });

        return $keyboard->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.in_menu_main'),
                'callback_data' => json_encode(['start', []])
            ])
        ]);
    }
}
