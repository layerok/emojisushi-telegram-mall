<?php

namespace Layerok\Tgmall\Features\Category;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Traits\Warn;
use Config;
use Telegram\Bot\FileUpload\InputFile;

class CategoryItemHandler extends Handler
{
    use Lang;
    use Warn;

    protected string $name = "category_item";

    public function validate():bool
    {
        $rules = [
            'page' => 'required|integer|min:1'
        ];

        $validation = Validator::make($this->arguments, $rules);

        if ($validation->fails()) {
            $this->errors =
                array_merge(
                    $validation->errors()->get('id'),
                    $validation->errors()->get('page')
                );
            return false;
        }

        return true;
    }

    public function run()
    {
        $this->ifDeleteMessage();
        $this->listProducts();

        $cart = EmojisushiApi::getCart();

        $markup = new CategoryFooterKeyboard([
            'cart' => $cart,
            'category_id' => $this->arguments['id'],
            'page' => $this->arguments['page']
        ]);

        $message = $this->replyWithMessage([
            'text' => self::lang('texts.triple_dot'),
            'reply_markup' => $markup->getKeyboard()
        ]);

        $msg_id = $message->messageId;

        $this->getState()->setDeleteMsgInCategory(['id' => $msg_id]);

        $this->getState()->setCartCountMsg([
            'id' => $msg_id,
            'category_id' => $this->arguments['id'],
            'page' => $this->arguments['page'],
            'count' => count($cart['data'])
        ]);
    }

    public function ifDeleteMessage()
    {
        if ($this->arguments['page'] > 1) {
            $deleteMsg = $this->getState()->getDeleteMsgInCategory();
            if ($deleteMsg) {

                $this->telegram->deleteMessage([
                    'chat_id' => $this->getChatId(),
                    'message_id' => $deleteMsg['id']
                ]);

                $this->getState()->setDeleteMsgInCategory(null);
            }
        }
    }

    public function listProducts()
    {
        $limit = Config::get('layerok.tgmall::settings.products.per_page', 10);
        $offset = ($this->arguments['page'] - 1) * $limit;

        $categories = EmojisushiApi::getCategories()['data'];

        $category = array_filter($categories, function($category) {
            return $this->arguments['id'] === $category['id'];
        })[0]; // todo: check if category exists first

        $products = EmojisushiApi::getProducts([
            'category_slug' => $category['slug'],
            'offset' => $offset,
            'limit' => $limit
        ])['data'];

        array_map(function ($product) {
            $this->sendProduct($product);
        }, $products);
    }

    public function sendProduct(array $product)
    {
        $markup = new CategoryProductKeyboard([
            'product' => $product
        ]);

        $caption = sprintf(
            "<b>%s</b>\n\n%s",
            $product['name'],
            \Html::strip($product['description_short'])
        );

        if(\Cache::has("telegram.files." . $product['id'])) {
            $this->replyWithPhoto([
                'photo' => \Cache::get("telegram.files." . $product['id']),
                'caption' => $caption,
                'reply_markup' => $markup->getKeyboard(),
                'parse_mode' => 'html',
            ]);
        } else if(isset($product['image_sets'][0]['images'][0]['path'])) {
            $response = $this->replyWithPhoto([
                'photo' => InputFile::create($product['image_sets'][0]['images'][0]['path']),
                'caption' => $caption,
                'reply_markup' => $markup->getKeyboard(),
                'parse_mode' => 'html',
            ]);

            \Cache::set(
                "telegram.files." . $product['id'],
                $response->getPhoto()->last()['file_id']
            );
        } else {
            $this->replyWithMessage([
                'text' => $caption,
                'reply_markup' => $markup->getKeyboard(),
                'parse_mode' => 'html',
            ]);
        }
    }
}
