<?php namespace  Layerok\Tgmall\Features\Category;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;

class CategoryItemsHandler extends Handler
{
    protected string $name = "category_items";

    public function run()
    {
        $replyWith = [
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.category'),
            'reply_markup' => (new CategoryItemsKeyboard(
                EmojisushiApi::getCategories()->data)
            )->getKeyboard()
        ];

        $this->replyWithMessage($replyWith);
    }
}
