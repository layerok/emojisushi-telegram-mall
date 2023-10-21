<?php namespace Layerok\TgMall\Features\Index;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;

use Event;

class MainMenuKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        Event::fire('tgmall.keyboard.main.beforeBuild', [$this]);
        $this->listen('afterAppend', function($event, $params) {
            $ci = $this->getColumnIndex();
            $ri = $this->getRowIndex();
            if($ri == 1 && $ci == 1) {
                $this->nextRow()
                    ->append([
                        'text' => \Lang::get('layerok.tgmall::lang.telegram.spots.change'),
                        'callback_data' => json_encode([
                            'list_spots', []
                        ])
                    ]);

            };


        });
        $this->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.categories'),
                'callback_data' => json_encode(['category_items', []])
            ])
            ->append([
                'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.cart'),
                'callback_data' => json_encode([
                    'cart',
                    ['type' => 'list']
                ])
            ])
            ->nextRow()
            ->append([
                'text' => '🌐 Вебсайт',
                'callback_data' => json_encode([
                    'website'
                ])
            ]);
    }

}
