<?php

namespace Layerok\TgMall\Objects;

class Spot {
    public ?string $id;
    public ?string $name;
    public ?string $address;
    public ?string $code;
    public ?string $cover;
    public ?string $created_at;
    public ?string $district;
    public ?Tablet $tablet;
    public ?string $frontend_url;
    public ?string $google_map_url;
    public ?string $html_content;
    public ?int $is_main;
    public ?string $phones;
    public ?int $poster_account_id;
    public ?int $poster_id;
    public ?string $slug;
    public ?int $published;
    public ?string $updated_at;
    public ?int $tablet_id;
    public ?int $chat_id;
    public ?int $city_id;
}
