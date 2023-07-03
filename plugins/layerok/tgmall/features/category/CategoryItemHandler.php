<?php

namespace Layerok\Tgmall\Features\Category;

use Event;
use Illuminate\Support\Facades\Validator;
use Layerok\PosterPos\Models\HideProduct;
use Layerok\PosterPos\Models\Spot;
use Layerok\TgMall\Classes\Callbacks\Handler;
use OFFLINE\Mall\Models\Product;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Traits\Warn;
use OFFLINE\Mall\Models\Category as CategoryModel;
use Config;
use Telegram\Bot\FileUpload\InputFile;

class CategoryItemHandler extends Handler
{
    use Lang;
    use Warn;

    protected $name = "category_item";

    public function validate():bool
    {
        $rules = [
            'id' => 'required|exists:offline_mall_categories,id',
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

        $markup = new CategoryFooterKeyboard([
            'cart' => $this->getCart(),
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
            'count' => $this->getCart()->products->count()
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
        $category = CategoryModel::where('id', '=', $this->arguments['id'])->first();

        $query = $category->products()->where('published', '=', '1');

;
        $state = $this->getState();
        $spot_id = $state->getSpotId();

        $spot = Spot::where('id', $spot_id)->first();

        $hidden = HideProduct::where([
            'spot_id' => $spot->id
        ])->pluck('product_id');

        $query->whereNotIn('offline_mall_products.id', $hidden);

        $query->limit($limit)
            ->offset($offset);


        $productsInCategory = $query->get();

        $productsInCategory->map(
            function (
                $product
            ) use (
                $productsInCategory,
                $limit
            ) {
                $this->sendProduct($product);
            }
        );
    }

    public function sendProduct(Product $product)
    {
        $markup = new CategoryProductKeyboard([
            'cart' => $this->getCart(),
            'product' => $product
        ]);

        $caption = $product->getCaptionForTelegram();
        $productImage = $product->image;

        $pathOrId = optional($productImage->tg)->file_id ?
            $productImage->tg->file_id :
            InputFile::create($productImage->path);


        if ($pathOrId) {
            $response = $this->replyWithPhoto([
                'photo' => $pathOrId,
                'caption' => $caption,
                'reply_markup' => $markup->getKeyboard(),
                'parse_mode' => 'html',
            ]);

            $product->image->setTelegramFileId($response);
        } else {
            $this->replyWithMessage([
                'text' => $caption,
                'reply_markup' => $markup->getKeyboard(),
                'parse_mode' => 'html',
            ]);
        }
    }

}
