<?php

namespace Layerok\TgMall\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Layerok\TgMall\Facades\Hydrator;
use Layerok\TgMall\Objects2\AppState;

class AsAppState implements CastsAttributes
{

    public function get($model, string $key, mixed $value, array $attributes): AppState
    {
        return Hydrator::hydrate(AppState::class, json_decode($value, true));
    }


    public function set($model, string $key, mixed $value, array $attributes): string
    {
        return json_encode(Hydrator::extract($value));
    }
}
