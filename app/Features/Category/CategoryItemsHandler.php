<?php namespace  App\Features\Category;

use App\Classes\Callbacks\Handler;
use App\Facades\EmojisushiApi;

class CategoryItemsHandler extends Handler
{
    protected string $name = "category_items";

    public function run()
    {
        $replyWith = [
            'text' => \Lang::get('lang.telegram.texts.category'),
            'reply_markup' => (new CategoryItemsKeyboard(
                EmojisushiApi::getCategories()->data)
            )->getKeyboard()
        ];

        $this->replyWithMessage($replyWith);
    }
}
