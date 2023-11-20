<?php

namespace Layerok\TgMall\Objects2;

class AppState {

    public ?string $message_handler = null;

    /**
     * @var CallbackHandler|null
     */
    public $callback_handler = null;

    public ?string $spot_id = null;
    public ?string $city_id = null;

    public ?string $session = null;
    /**
     * @var CartTotalMsg|null
     */
    public $cart_total_msg = null;

    /**
     * @var CartCountMsg|null
     */
    public $cart_count_msg = null;

    /**
     * @var DeleteMsgInCategory|null
     */
    public $delete_msg_in_category = null;

    /** @var Order */
    public $order = null;
}
