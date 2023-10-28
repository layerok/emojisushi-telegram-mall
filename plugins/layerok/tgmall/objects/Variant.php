<?php

namespace Layerok\TgMall\Objects;

class Variant {
    public string $id;

    public string $description;

    public bool $published;

    public ?int $poster_id;

    public Product $product;

    public int $product_id;

    /**
     * @var Price[]
     */
    public array $prices;
}
