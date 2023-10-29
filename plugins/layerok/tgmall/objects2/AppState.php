<?php

namespace Layerok\TgMall\Objects2;

class AppState {

    public ?string $message_handler;

    /**
     * @var CallbackHandler|null
     */
    public $callback_handler;

    public ?string $spot_id;

    public ?string $session;
    /**
     * @var CartTotalMsg|null
     */
    public $cart_total_msg;

    /**
     * @var CartCountMsg|null
     */
    public $cart_count_msg;

    /**
     * @var DeleteMsgInCategory|null
     */
    public $delete_msg_in_category;

    /** @var Order */
    public $order;
}
