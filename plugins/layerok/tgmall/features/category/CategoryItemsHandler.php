<?php namespace  Layerok\Tgmall\Features\Category;

use Layerok\PosterPos\Classes\RootCategory;
use Layerok\PosterPos\Models\HideCategory;
use Layerok\PosterPos\Models\Spot;
use Layerok\TgMall\Classes\Callbacks\Handler;
use Layerok\TgMall\Classes\Traits\CallbackData;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Models\Category;

class CategoryItemsHandler extends Handler
{
    use Lang;
    use CallbackData;

    protected $name = "category_items";


    public function run()
    {
        $query = Category::where('published', 1);

        $state = $this->getState();
        $spot_id = $state->getSpotId();

        $spot = Spot::where('id', $spot_id)->first();

        $root = Category::where([
            ['slug', RootCategory::SLUG_KEY],

        ])->first();

        $hidden = HideCategory::where([
            'spot_id' => $spot->id
        ])->pluck('category_id');

        $query->where([
            ['parent_id', $root->id],
        ])->whereNotIn('id', $hidden);

        $categories = $query->get();
        $markup = new CategoryItemsKeyboard([
            'categories' => $categories
        ]);
        $replyWith = [
            'text' => self::lang('texts.category'),
            'reply_markup' => $markup->getKeyboard()
        ];

        $this->replyWithMessage($replyWith);
    }
}
