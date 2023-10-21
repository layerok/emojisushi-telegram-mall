<?php namespace  Layerok\Tgmall\Features\Category;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;

class CategoryItemsHandler extends Handler
{
    protected string $name = "category_items";

    public function run()
    {
        $keyboard = new CategoryItemsKeyboard([
            'categories' => EmojisushiApi::getCategories()['data']
        ]);

        $keyboard->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.in_menu_main'),
            'callback_data' => json_encode(['start', []])
        ]);

        $replyWith = [
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.category'),
            'reply_markup' => $keyboard->getKeyboard()
        ];

        $this->replyWithMessage($replyWith);
    }
}
