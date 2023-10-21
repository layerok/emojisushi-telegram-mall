<?php namespace Layerok\TgMall\Features\Checkout\Keyboards;

use Layerok\TgMall\Classes\Keyboards\InlineKeyboard;

class PreparePaymentChangeKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        $this->append([
            'text' => 'Так',
            'callback_data' => json_encode([
                'prepare_payment_change',
                []
            ])
        ])->append([
            'text' => 'Ні',
            'callback_data' => json_encode([
                'list_delivery_methods',
                []
            ])
        ]);

    }
}
