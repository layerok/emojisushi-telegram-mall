<?php

namespace Layerok\TgMall\Objects;

class District {
    public int $id;
    public string $name;
    public string $created_at;
    public string $updated_at;
    /**
     * @var Spot[]
     */
    public $spots;
}
