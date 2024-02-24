<?php

namespace App\Facades;

use App\Objects\Cart;
use App\Objects\CartProduct;
use App\Objects\CategoriesList;
use App\Objects\Category;
use App\Objects\CitiesList;
use App\Objects\City;
use App\Objects\PaymentMethod;
use App\Objects\PaymentMethodsList;
use App\Objects\Product;
use App\Objects\ProductsList;
use App\Objects\ShipmentMethod;
use App\Objects\ShipmentMethodsList;
use App\Objects\Spot;
use App\Objects\SpotsList;
use App\Objects\Variant;
use App\Objects2\PlaceOrderResponse;
use Illuminate\Support\Facades\Facade;

/**
 * Class EmojisushiApi
 *
 * @method static void init(array $conf)
 * @method static void setHeader(string $name, string $value)
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
 * @method static PlaceOrderResponse placeOrder(array $params = [], array $guzzleOptions = [])
 *
 *
 * @see  \App\Services\EmojisushiApi;
 */

class EmojisushiApi extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'emojisushi.api';
    }
}
