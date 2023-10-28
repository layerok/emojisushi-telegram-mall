<?php

namespace Layerok\TgMall\Objects;

class CartProduct {
    public int $id;
    public string $name;
    public int $product_id;
    public ?int $variant_id;
    /**
     * @var Variant|null
     */
    public $variant;
    /**
     * @var Product
     */
    public $product;
    public int $quantity;
    public array $price;
}
