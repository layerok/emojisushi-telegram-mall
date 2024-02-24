<?php

namespace App\Objects;

class PaymentMethodsList {
    /**
     * @var PaymentMethod[]
     */
    public array $data;

    /**
     * @var PaginationMeta
     */
    public $meta;
}
