<?php namespace  Layerok\Tgmall\Features\Category;

use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;
use Layerok\TgMall\Classes\Traits\CallbackData;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Facades\EmojisushiApi;

class CategoryItemsHandler extends Handler
{
    use Lang;
    use CallbackData;

    protected string $name = "category_items";


    public function run()
    {
        $keyboard = new CategoryItemsKeyboard([
            'categories' => EmojisushiApi::getCategories()['data']
        ]);

        $keyboard->append([
            'text' => self::lang('buttons.in_menu_main'),
            'callback_data' => self::prepareCallbackData('start')
        ]);

        $replyWith = [
            'text' => self::lang('texts.category'),
            'reply_markup' => $keyboard->getKeyboard()
        ];

        $this->replyWithMessage($replyWith);
    }
}
