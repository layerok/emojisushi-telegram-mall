<?php namespace Layerok\TgMall\Features\Category;

use Layerok\TgMall\Facades\EmojisushiApi;
use Layerok\TgMall\Objects\Product;
use Layerok\TgMall\Objects\Variant;
use Telegram\Bot\Keyboard\Keyboard;

class CategoryProductKeyboard
{
    public function __construct(public Product $product)
    {

    }

    public function getKeyboard(): Keyboard
    {
        $keyboard = (new Keyboard())->inline();

        if($this->product->inventory_management_method === 'variant') {
            $variants = array_filter($this->product->variants, function(Variant $variant) {
                return $variant->published;
            });

            collect($variants)->each(function(Variant $variant) use ($keyboard) {
                $cartProduct = EmojisushiApi::getCartProduct([
                    'product_id' => $variant->product->id,
                    'variant_id' => $variant->id
                ]);

                $keyboard->row([
                    Keyboard::inlineButton([
                        'text' => sprintf(
                            "%s: %s",
                            \Lang::get('layerok.tgmall::lang.telegram.buttons.price'),
                            $variant->prices[0]->price_formatted
                        ),
                        'callback_data' => json_encode(['noop', []])
                    ]),
                    Keyboard::inlineButton([
                        'text' => $variant->description,
                        'callback_data' => json_encode(['noop', []])
                    ]),
                    !!$cartProduct ? Keyboard::inlineButton([
                        'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.added_to_cart'),
                        'callback_data' => json_encode(['noop', []])
                    ]): Keyboard::inlineButton([
                        'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.add_to_cart'),
                        'callback_data' => json_encode([
                            'product_add',
                            [
                                'qty' =>  1,
                                'variant_id' => $variant->id
                            ]
                        ])
                    ])
                ])->row([]);
            });

        } else {
            $cartProduct = EmojisushiApi::getCartProduct([
                'product_id' => $this->product->id,
            ]);

            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => sprintf(
                        "%s: %s",
                        \Lang::get('layerok.tgmall::lang.telegram.buttons.price'),
                        $this->product->prices[0]->price_formatted
                    ),
                    'callback_data' => json_encode(['noop', []])
                ]),
                !!$cartProduct ? Keyboard::inlineButton([
                    'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.added_to_cart'),
                    'callback_data' => json_encode(['noop', []])
                ]): Keyboard::inlineButton([
                    'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.add_to_cart'),
                    'callback_data' => json_encode([
                        'product_add',
                        ['qty' =>  1, 'product_id' => $this->product->id]
                    ])
                ])
            ])->row([]);
        }

        return $keyboard;
    }

}
