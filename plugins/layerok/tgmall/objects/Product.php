<?php

namespace Layerok\TgMall\Objects;

class Product {
    public int $id;

    public string $name;

    public ?string $description;

    public ?int $poster_id;

    public ?string $description_short;

    public string $slug;
    /**
     * @var ImageSet[]
     */
    public array $image_sets;

    /**
     * @var Variant[]
     */
    public array $variants;

    public string $inventory_management_method;

    /**
     * @var Price[]
     */
    public array $prices;
}
