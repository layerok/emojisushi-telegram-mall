<?php namespace App\Features\Index;

use Telegram\Bot\Keyboard\Keyboard;

class MainMenuKeyboard
{
    public function getKeyboard(): Keyboard
    {
        return (new Keyboard())->inline()->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('lang.telegram.buttons.categories'),
                'callback_data' => json_encode(['category_items', []])
            ]),
            Keyboard::inlineButton([
                'text' => \Lang::get('lang.telegram.buttons.cart'),
                'callback_data' => json_encode([
                    'cart',
                    ['type' => 'list']
                ])
            ])
        ])->row([])->row([
            Keyboard::inlineButton([
                'text' => '🌐 Вебсайт',
                'callback_data' => json_encode([
                    'website', []
                ])
            ])
        ])->row([])->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('lang.telegram.cities.change'),
                'callback_data' => json_encode([
                    'list_cities', []
                ])
            ])
        ]);
    }

}
