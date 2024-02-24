<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class Hydrator
 *
 * @method static hydrate(string $type, array $data = [])
 * @method static array extract($obj)
 *
 * @see  \App\Services\Hydrator;
 */

class Hydrator extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'hydrator';
    }
}
