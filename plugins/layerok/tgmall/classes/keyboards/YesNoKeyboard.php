<?php

namespace Layerok\TgMall\Classes\Keyboards;


class YesNoKeyboard extends InlineKeyboard
{
    public function build(): void
    {
        $this->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.yes'),
            'callback_data' => json_encode([$this->vars['yes']['handler'], []]),
        ])->append([
            'text' => \Lang::get('layerok.tgmall::lang.telegram.buttons.no'),
            'callback_data' => json_encode([$this->vars['no']['handler'], []])
        ]);
    }
}
