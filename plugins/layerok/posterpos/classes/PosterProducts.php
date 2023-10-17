<?php

namespace Layerok\PosterPos\Classes;

class PosterProducts
{
    protected array $items = [];


    public function __construct($items = [])
    {
        $this->items = $items;
    }

    public function add($name, $product_id, $count, $modificator_id = null): PosterProducts
    {
        $item = [
            'name' => $name,
            'product_id' => $product_id,
            'count' => $count
        ];

        if (isset($modificator_id)) {
            $item['modificator_id'] = $modificator_id;
        }

        $this->items[] = $item;
        return $this;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function addProduct($id, $name, $count): PosterProducts
    {
        if (!isset($count)) {
            return $this;
        }
        if (intval($count) > 0) {
            $this->add($name, $id, intval($count));
        }
        return $this;
    }

    public function addCartProduct(array $cartProduct): PosterProducts
    {
        return $this->add(
            $cartProduct['product']['name'],
            $cartProduct['product']['poster_id'],
            $cartProduct['quantity'],
            $cartProduct['variant']['poster_id'] ?? null
        );
    }

    public function addCartProducts($products): PosterProducts
    {
        foreach ($products as $p) {
            $this->addCartProduct($p);
        }
        return $this;
    }



}
