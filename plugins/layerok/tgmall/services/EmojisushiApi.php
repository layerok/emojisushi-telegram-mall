<?php

namespace Layerok\TgMall\Services;

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
     * @param array $params {
     *   slug_or_id
     * }
     * @param array $guzzleOptions
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @param array $params {
     *   slug_or_id
     * }
     * @param array $guzzleOptions
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @param array $params {
     *  offset,
     *  limit,
     *  category_slug
     * }
     * @param array $guzzleOptions
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @param array $params {
     *  product_id,
     * }
     * @param array $guzzleOptions
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProduct(array $params = [], array $guzzleOptions = []) {
        $products = $this->getProducts(['limit' => 44543534, 'category_slug' => 'menu'])['data'];
        $found = array_filter(
            $products,
            function($p) use($params) { return $params['product_id'] == $p['id']; }
        );

        return array_values($found)[0] ?? null;
    }

    /**
     * @param array $params {
     *  variant_id,
     * }
     * @param array $guzzleOptions
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getVariant(array $params = [], array $guzzleOptions = []) {
        $products = $this->getProducts(['limit' => 44543534, 'category_slug' => 'menu'])['data'];
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
     * @param array $params {
     *  includeSpots,
     *  includeDistricts
     * }
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @param array $params {
     *  product_id,
     *  quantity,
     *  variant_id
     * }
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @param array $params {
     *  cart_product_id,
     * }
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @param array $params {
     *  product_id,
     *  variant_id
     * }
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCartProduct(array $params = [], array $guzzleOptions = []) {
        $cart = $this->getCart();
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
}
