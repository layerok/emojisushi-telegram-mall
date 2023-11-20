<?php

namespace Layerok\TgMall\Facades;

use Illuminate\Support\Facades\Facade;
use Layerok\TgMall\Objects\Cart;
use Layerok\TgMall\Objects\CartProduct;
use Layerok\TgMall\Objects\CategoriesList;
use Layerok\TgMall\Objects\Category;
use Layerok\TgMall\Objects\CitiesList;
use Layerok\TgMall\Objects\City;
use Layerok\TgMall\Objects\PaymentMethod;
use Layerok\TgMall\Objects\PaymentMethodsList;
use Layerok\TgMall\Objects\Product;
use Layerok\TgMall\Objects\ProductsList;
use Layerok\TgMall\Objects\ShipmentMethod;
use Layerok\TgMall\Objects\ShipmentMethodsList;
use Layerok\TgMall\Objects\Spot;
use Layerok\TgMall\Objects\SpotsList;
use Layerok\TgMall\Objects\Variant;

/**
 * Class EmojisushiApi
 *
 * @method static void init(array $conf)
 * @method static City getCity(array $params = [], array $guzzleOptions = [])
 * @method static CitiesList getCities(array $params = [], array $guzzleOptions = [])
 * @method static Spot getSpot(array $params = [], array $guzzleOptions = [])
 * @method static SpotsList getSpots(array $params = [], array $guzzleOptions = [])
 * @method static CategoriesList getCategories(array $params = [], array $guzzleOptions = [])
 * @method static Category getCategory(array $params = [], array $guzzleOptions = [])
 * @method static ProductsList getProducts(array $params = [], array $guzzleOptions = [])
 * @method static Product getProduct(array $params = [], array $guzzleOptions = [])
 * @method static Variant getVariant(array $params = [], array $guzzleOptions = [])
 * @method static Cart getCart(array $params = [], array $guzzleOptions = [])
 * @method static CartProduct getCartProduct(array $params = [], array $guzzleOptions = [])
 * @method static Cart addCartProduct(array $params = [], array $guzzleOptions = [])
 * @method static Cart removeCartProduct(array $params = [], array $guzzleOptions = [])
 * @method static PaymentMethodsList getPaymentMethods(array $params = [], array $guzzleOptions = [])
 * @method static PaymentMethod getPaymentMethod(array $params = [], array $guzzleOptions = [])
 * @method static ShipmentMethodsList getShippingMethods(array $params = [], array $guzzleOptions = [])
 * @method static ShipmentMethod getShippingMethod(array $params = [], array $guzzleOptions = [])
 * @method static Cart clearCart(array $params = [], array $guzzleOptions = [])
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
