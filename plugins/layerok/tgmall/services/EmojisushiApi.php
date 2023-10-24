<?php

namespace Layerok\TgMall\Services;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use \GuzzleHttp\Client;

class EmojisushiApi {

    protected Client $guzzleClient;

    protected string $lang;

    protected string $sessionId;

    protected string $baseUrl;

    protected HandlerStack $handler;

    public function init($conf) {
        $this->sessionId = $conf['sessionId'];
        $this->lang = $conf['lang'] ?? 'uk';
        $this->baseUrl = $config['baseUrl'] ?? 'https://api.emojisushi.com.ua/api/';

        $this->handler = HandlerStack::create();

        $this->handler->push(Middleware::mapRequest(function (RequestInterface $request) {
            if($this->sessionId) {
                $request = Utils::modifyRequest($request, [
                    'set_headers' => [
                        'X-Session-Id' => $this->sessionId
                    ]
                ]);
            }

           return Utils::modifyRequest($request, [
               'query' => ($query = $request->getUri()->getQuery()) .
                   (strlen($query) ? '&': '?') . 'lang=' . $this->lang
            ]);
        }));

        $baseConfig = [
            'base_uri' => $this->baseUrl,
            'handler' => $this->handler,
            'verify' => false
        ];

        $this->guzzleClient = new Client($baseConfig);
    }

    /**
     * @param array{slug_or_id: string|int} $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function getCity( array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->get(
            'city',
            array_merge_recursive($guzzleOptions, [
                'query' => $params,
            ]),
        );
        return json_decode($res->getBody(), true);
    }

    /**
     * @param array{slug_or_id: string|int} $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function getSpot( array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->get(
            'spot',
            array_merge_recursive($guzzleOptions, [
                'query' => $params,
            ]),
        );
        return json_decode($res->getBody(), true);
    }

    public function getSpots( array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->get(
            'spots',
            array_merge_recursive($guzzleOptions, [
                'query' => [],
            ]),
        );
        return json_decode($res->getBody(), true);
    }

    public function getCategories( array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->get(
            'categories',
            array_merge_recursive($guzzleOptions, [
                'query' => [],
            ]),
        );
        return json_decode($res->getBody(), true);
    }

    /**
     * @param array{id: string|int} $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function getCategory(array $params = [], array $guzzleOptions = []) {
        $products = $this->getCategories(
            ['limit' => 44543534],
            $guzzleOptions
        )['data'];
        $found = array_filter(
            $products,
            function($category) use($params) { return $params['id'] == $category['id']; }
        );

        return array_values($found)[0] ?? null;
    }

    /**
     * @param array{offset?: string|int, limit?: string|int} $params
     * $param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function getProducts(array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->get(
            'products',
            array_merge_recursive($guzzleOptions, [
                'query' => array_merge($params, []),
            ]),
        );
        return json_decode($res->getBody(), true);
    }

    /**
     * @param array{product_id: string|int} $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function getProduct(array $params = [], array $guzzleOptions = []) {
        $products = $this->getProducts(
            ['limit' => 44543534, 'category_slug' => 'menu'],
            $guzzleOptions
        )['data'];
        $found = array_filter(
            $products,
            function($product) use($params) { return $params['product_id'] == $product['id']; }
        );

        return array_values($found)[0] ?? null;
    }

    /**
     * @param array{variant_id: string|int} $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function getVariant(array $params = [], array $guzzleOptions = []) {
        $products = $this->getProducts(['limit' => 44543534, 'category_slug' => 'menu'], $guzzleOptions)['data'];
        $found = array_filter($products, function ($product) use($params) {
            return in_array($params['variant_id'], array_map(fn($v) => $v['id'], $product['variants']));
        })[0] ?? null;

        if(count($found) === 0) {
            return null;
        }

        $found = array_values($found);

        return array_filter(
            $found[0]['variants'],
            fn($v) => $v['id'] === $params['variant_id']
        )[0] ?? null;
    }

    /**
     * @param array{includeSpots?: bool,includeDistricts?:bool} $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function getCities( array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->get(
            'city',
            array_merge_recursive($guzzleOptions, [
                'query' => $params,
            ]),
        );
        return json_decode($res->getBody(), true);
    }

    public function getCart(array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->get(
            'cart/products',
            array_merge_recursive($guzzleOptions, [
                'query' => $params
            ]),
        );
        return json_decode($res->getBody(), true);
    }

    /**
     * @param array{product_id:string|int, quantity:string|int, variant_id:string|int} $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function addCartProduct(array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->post(
            'cart/add',
            array_merge_recursive($guzzleOptions, [
                'json' => $params
            ]),
        );
        return json_decode($res->getBody(), true);
    }

    /**
     * @param array{cart_product_id: string|int } $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function removeFromCart(array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->post(
            'cart/remove',
            array_merge_recursive($guzzleOptions, [
                'json' => $params
            ]),
        );
        return json_decode($res->getBody(), true);
    }

    /**
     * @param array{product_id: string|int, variant_id: string|int} $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function getCartProduct(array $params = [], array $guzzleOptions = []) {
        $cart = $this->getCart([], $guzzleOptions);
        $found = array_filter($cart['data'], function($cartProduct) use($params) {
            if(isset($params['variant_id'])) {
                if($cartProduct['variant_id'] !== $params['variant_id']) {
                    return false;
                }
            }
            return $params['product_id'] === $cartProduct['product_id'];
        });

        $found = array_values($found);

        return $found[0] ?? null;
    }

    /**
     * @param array{offset?: string|int, limit?: string|int} $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function getPaymentMethods(array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->get(
            'payments',
            array_merge_recursive($guzzleOptions, [
                'query' => $params
            ]),
        );
        return json_decode($res->getBody(), true);
    }

    /**
     * @param array{offset?: string|int, limit?: string|int} $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function getShippingMethods(array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->get(
            'shipping',
            array_merge_recursive($guzzleOptions, [
                'query' => $params
            ]),
        );
        return json_decode($res->getBody(), true);
    }

    /**
     * @param array{id:string|int} $params
     * @param array $guzzleOptions
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getShippingMethod(array $params = [], array $guzzleOptions = []) {
        $methods = $this->getShippingMethods([
            'limit' => 4342342342
        ], $guzzleOptions)['data'];
        $found = array_filter($methods, function($method) use($params) {
            return $method['id'] === $params['id'];
        });
        return array_values($found)[0] ?? null;
    }

    /**
     * @param array{id:string|int} $params
     * @param array $guzzleOptions
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getPaymentMethod(array $params = [], array $guzzleOptions = []) {
        $methods = $this->getPaymentMethods([
            'limit' => 4342342342
        ], $guzzleOptions)['data'];
        $found = array_filter($methods, function($method) use($params) {
            return $method['id'] === $params['id'];
        });
        return array_values($found)[0] ?? null;
    }

    /**
     * @param array $params
     * @param array $guzzleOptions
     * @return mixed
     * @throws GuzzleException
     */
    public function clearCart(array $params = [], array $guzzleOptions = []) {
        $res = $this->guzzleClient->post(
            'cart/clear',
            array_merge_recursive($guzzleOptions, [
                'json' => $params
            ]),
        );
        return json_decode($res->getBody(), true);
    }
}
