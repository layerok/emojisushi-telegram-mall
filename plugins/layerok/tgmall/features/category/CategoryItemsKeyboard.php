<?php namespace Layerok\Tgmall\Features\Category;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Objects\Category;

class CategoryItemsKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        collect($this->vars['categories'])->map(function (Category $category) {
            $this->append(
                [
                    'text' => $category->name,
                    'callback_data' => json_encode([
                        'category_item',
                        [
                            'id' => $category->id,
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
