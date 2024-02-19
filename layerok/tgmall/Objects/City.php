<?php

namespace Layerok\TgMall\Objects;

class City {
    public int $id;
    public string $name;
    public string $phones;
    /**
     * @var Spot[]
     */
    public $spots;
    public int $is_main;
    public string $created_at;
    public string $thankyou_page_url;
    public string $update_at;
    public string $slug;
    public string $google_map_url;
    public string $frontend_url;
    /**
     * @var District[]
     */
    public $districts;
}
