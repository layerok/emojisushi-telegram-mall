<?php

namespace App\Casts;

use App\Facades\Hydrator;
use App\Objects2\AppState;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

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
