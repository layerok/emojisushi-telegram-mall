<?php

namespace App\Classes\Keyboards;

use Telegram\Bot\Keyboard\Keyboard;

class CounterKeyboard
{
    public function __construct(
        public $count,
        public string $okHandler,
        public float $min = 0,
        public float $max = INF
    )
    {

    }

    public function getKeyboard(): Keyboard
    {
        return (new Keyboard())->inline()->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('lang.telegram.buttons.minus'),
                'callback_data' => json_encode([
                    $this->count - 1 < $this->min ? 'noop': 'counter_update',
                    [max($this->count - 1, $this->min)]
                ])
            ]),
            Keyboard::inlineButton([
                'text' => $this->count,
                'callback_data' => json_encode(['noop', []])
            ]),
            Keyboard::inlineButton([
                'text' => \Lang::get('lang.telegram.buttons.plus'),
                'callback_data' => json_encode(['counter_update', [min($this->count + 1, $this->max)]])
            ])
        ])->row([
            Keyboard::inlineButton([
                'text' => \Lang::get('lang.telegram.buttons.next'),
                'callback_data' => json_encode([$this->okHandler, [$this->count]])
            ])
        ]);
    }
}
