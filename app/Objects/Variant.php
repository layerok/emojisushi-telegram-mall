<?php

namespace App\Objects;

class Variant {
    public int $id;

    public string $description;

    public bool $published;

    public Product $product;

    public int $product_id;

    /**
     * @var Price[]
     */
    public array $prices;
}
