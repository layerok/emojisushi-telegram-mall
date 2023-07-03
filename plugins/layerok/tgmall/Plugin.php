<?php
namespace Layerok\TgMall;

use Layerok\TgMall\Classes\Boot\Events;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Events\TgMallOrderHandler;
use Layerok\TgMall\Models\File as TelegramFile;
use OFFLINE\Mall\Models\Product;
use System\Classes\PluginBase;
use System\Models\File as SystemFile;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Objects\Message;
use Event;

class Plugin extends PluginBase
{
    public $require = ['OFFLINE.Mall', 'Layerok.BaseCode'];
    use Lang;

    public function boot() {
        Events::boot();
        Event::subscribe(new TgMallOrderHandler());
    }

    public function register()
    {
        $this->registerConsoleCommand('create:tg.mall.handler', \Layerok\TgMall\Console\CreateCallbackHandler::class);
        $this->registerConsoleCommand('create:tg.mall.keyboard', \Layerok\TgMall\Console\CreateKeyboard::class);

        SystemFile::extend(function($model) {
            $model->hasOne['tg'] = [TelegramFile::class, 'key' => 'system_file_id'];

            $model->addDynamicMethod('getTelegramFileId', function () use ($model) {
                return optional($model->tg)->file_id ?
                    $model->tg->file_id :
                    InputFile::create($model->path);
            });

            $model->addDynamicMethod('setTelegramFileId', function (Message $response) use ($model) {

                $photoObject = $response->getPhoto();

                if (!$photoObject) {
                    return;
                }

                $last = $photoObject->last();

                if (!isset($last)) {
                    return;
                }

                $file_id = $last['file_id'];

                if(optional($model->tg)->file_id){
                    return;
                }

                $telegramFile = new TelegramFile();
                $telegramFile->system_file_id = $model->id;
                $telegramFile->file_id = $file_id;
                $telegramFile->save();

            });
        });

    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'EmojiSushiBot settings',
                'description' => 'Manage bot settings.',
                'category' => 'Telegram',
                'icon' => 'icon-cog',
                'class' => \Layerok\TgMall\Models\Settings::class,
                'order' => 500,
                'keywords' => 'telegram bot',
            ]
        ];
    }


}
