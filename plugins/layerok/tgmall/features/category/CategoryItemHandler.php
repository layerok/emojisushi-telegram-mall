<?php

namespace Layerok\Tgmall\Features\Category;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\StateKeys;
use Layerok\TgMall\Facades\EmojisushiApi;
use Config;
use Layerok\TgMall\Objects\Product;
use Telegram\Bot\FileUpload\InputFile;

class CategoryItemHandler extends Handler
{
    protected string $name = "category_item";

    public function run()
    {
        Validator::validate($this->arguments, [
            'page' => 'required|integer|min:1'
        ]);

        if ($this->arguments['page'] > 1) {
            $deleteMsg = $this->getUser()->state->getStateValue(StateKeys::DELETE_MSG_IN_CATEGORY);
            if ($deleteMsg) {

                $this->api->deleteMessage([
                    'chat_id' => $this->getUpdate()->getChat()->id,
                    'message_id' => $deleteMsg['id']
                ]);

                $this->getUser()->state->setStateValue(StateKeys::CALLBACK_HANDLER, null);
            }
        }

        $this->listProducts();

        $cart = EmojisushiApi::getCart();

        $markup = new CategoryFooterKeyboard([
            'cart' => $cart,
            'category_id' => $this->arguments['id'],
            'page' => $this->arguments['page']
        ]);

        $message = $this->replyWithMessage([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.texts.triple_dot'),
            'reply_markup' => $markup->getKeyboard()
        ]);

        $this->getUser()->state->setStateValue(StateKeys::CALLBACK_HANDLER, ['id' => $message->messageId]);

        $this->getUser()->state->setStateValue(StateKeys::CART_COUNT_MSG, [
            'id' => $message->messageId,
            'category_id' => $this->arguments['id'],
            'page' => $this->arguments['page'],
            'count' => count($cart->data)
        ]);
    }


    public function listProducts()
    {
        $limit = Config::get('layerok.tgmall::settings.products.per_page', 10);
        $offset = ($this->arguments['page'] - 1) * $limit;

        $category = EmojisushiApi::getCategory(['id' => $this->arguments['id']]);

        $products = EmojisushiApi::getProducts([
            'category_slug' => $category->slug,
            'offset' => $offset,
            'limit' => $limit
        ])->data;

        collect($products)->each(function (Product $product) {
            $this->sendProduct($product);
        });
    }

    public function sendProduct(Product $product)
    {
        $markup = new CategoryProductKeyboard([
            'product' => $product
        ]);

        $caption = sprintf(
            "<b>%s</b>\n\n%s",
            $product->name,
            \Html::strip($product->description_short)
        );

        if(\Cache::has("telegram.files." . $product->id)) {
            $this->replyWithPhoto([
                'photo' => \Cache::get("telegram.files." . $product->id),
                'caption' => $caption,
                'reply_markup' => $markup->getKeyboard(),
                'parse_mode' => 'html',
            ]);
        } else if(isset($product->image_sets[0]->images[0]->path)) {
            $response = $this->replyWithPhoto([
                'photo' => InputFile::create($product->image_sets[0]->images[0]->path),
                'caption' => $caption,
                'reply_markup' => $markup->getKeyboard(),
                'parse_mode' => 'html',
            ]);

            \Cache::set(
                "telegram.files." . $product->id,
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
