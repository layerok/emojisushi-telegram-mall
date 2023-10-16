<?php

namespace Layerok\TgMall\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class EmojisushiApi
 *
 * @method static void init(array $conf)
 * @method static array getCity(array $params = [], array $guzzleOptions = [])
 * @method static array getSpot(array $params = [], array $guzzleOptions = [])
 * @method static array getSpots(array $params = [], array $guzzleOptions = [])
 * @method static array getCategories(array $params = [], array $guzzleOptions = [])
 * @method static array getProducts(array $params = [], array $guzzleOptions = [])
 * @method static array getProduct(array $params = [], array $guzzleOptions = [])
 * @method static array getVariant(array $params = [], array $guzzleOptions = [])
 * @method static array getCart(array $params = [], array $guzzleOptions = [])
 * @method static array getCartProduct(array $params = [], array $guzzleOptions = [])
 * @method static array addCartProduct(array $params = [], array $guzzleOptions = [])
 *
 *
 * @see  \Layerok\TgMall\Services\EmojisushiApi;
 */

class EmojisushiApi extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'emojisushi.api';
    }
}
