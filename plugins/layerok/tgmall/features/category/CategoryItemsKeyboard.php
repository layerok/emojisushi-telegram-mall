<?php namespace Layerok\Tgmall\Features\Category;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;

class CategoryItemsKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        collect($this->vars['categories'])->map(function ($row) {
            $this->append(
                [
                    'text' => $row['name'],
                    'callback_data' => json_encode([
                        'category_item',
                        [
                            'id' => $row['id'],
                            'page' => 1
                        ]
                    ])
                ]
            )->nextRow();
        });

        $this->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.in_menu_main'),
            'callback_data' => json_encode(['start', []])
        ]);
    }
}
