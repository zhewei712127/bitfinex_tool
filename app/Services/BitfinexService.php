<?php

namespace App\Services;


use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Crypt;

class BitfinexService extends GuzzleService
{
    private $api_key = null;
    private $api_secret = null;

    public function __construct()
    {
        $this->api_key = isset(auth()->user()->connection) ? Crypt::decrypt(auth()->user()->connection->api_key) : '';
        $this->api_secret = isset(auth()->user()->connection) ? Crypt::decrypt(auth()->user()->connection->api_secret) : '';
    }

    public function tickers($symbols)
    {
        $params = [
            'symbols' => $symbols
        ];

        return $this->apiGet('public.tickers', $params);
    }

    public function book($symbol)
    {
        $type = [
            'type' => 'public.book',
            'replace' => [
                '%SYMBOL%' => $symbol
            ]
        ];

        return $this->apiGet($type, []);
    }

    public function wallets()
    {
        return $this->apiPost('auth.wallets', []);
    }

    public function userInfo()
    {
        return $this->apiPost('auth.user_info', []);
    }

    public function fundingLoans($symbols)
    {
        $type = [
            'type' => 'auth.funding_loans',
            'replace' => [
                '%SYMBOL%' => $symbols
            ]
        ];

        return $this->apiPost($type, []);
    }

    public function fundingLoansHistory($symbols)
    {
        $type = [
            'type' => 'auth.funding_loans_history',
            'replace' => [
                '%SYMBOL%' => $symbols
            ]
        ];

        return $this->apiPost($type, []);
    }

    public function ordersHistory()
    {
        return $this->apiPost('auth.orders_history', []);
    }

    public function orderCreate($data)
    {
        return $this->apiPost('auth.order_submit', $data);
    }

    public function transfer($data)
    {
        return $this->apiPost('auth.transfer', $data);
    }

    private function apiPost($type, $body)
    {
        if (!is_array($type)) $type = ['type' => $type];

        $url = config("bitfinex.{$type['type']}");

        if (isset($type['replace'])) $url = strtr($url, $type['replace']);

        $nonce = $nonce = strval(time() * 1000000000);

        $path = str_replace(config('bitfinex.auth.url'), '', $url);

        $signature = "/api/{$path}{$nonce}" . json_encode($body);

        $signature = hash_hmac("sha384", utf8_encode($signature), utf8_encode($this->api_secret));

        $request = [
            'headers' => [
                'bfx-nonce' => $nonce,
                'bfx-apikey' => $this->api_key,
                'bfx-signature' => $signature,
            ],
            'body' => $body
        ];

        $ret = [
            'status' => null,
            'url' => $url,
            'request' => $request,
            'response' => '',
            'note' => 'OK',
            'body' => $body
        ];

        \Log::info("bitfinex_api_{$type['type']}_start", ['request' => $ret]);

        try {
            $response = parent::post($request, $url);

            $ret['status'] = true;
            $ret['response'] = $response;

            \Log::info("bitfinex_api_{$type['type']}_success", ['request' => $ret]);

            return $ret;

        } catch (RequestException $exception) {

            $ret['status'] = false;

            $res = (!$exception->getResponse())
                ? null
                : $exception->getResponse()->getBody()->getContents();

            $ret['note'] = $res;

            \Log::info("bitfinex_api_{$type['type']}_requestException", ['request' => $ret]);
            return $ret;
        }
    }

    private function apiGet($type, $params = [])
    {
        if (!is_array($type)) $type = ['type' => $type];

        $url = config("bitfinex.{$type['type']}");

        if (isset($type['replace'])) $url = strtr($url, $type['replace']);

        $request = [];

        if (count($params) > 0) {
            $request['query'] = $params;
        }

        $ret = [
            'status' => null,
            'url' => $url,
            'request' => $request,
            'response' => '',
            'note' => 'OK'
        ];

        \Log::info("bitfinex_api_{$type['type']}_start", ['request' => $ret]);

        try {
            $response = parent::get($request, $url);

            $ret['status'] = true;
            $ret['response'] = $response;

            \Log::info("bitfinex_api_{$type['type']}_success", ['request' => $ret]);

            return $ret;

        } catch (RequestException $exception) {

            $ret['status'] = false;

            $res = (!$exception->getResponse())
                ? null
                : $exception->getResponse()->getBody()->getContents();

            $ret['note'] = $res;

            \Log::info("bitfinex_api_{$type['type']}_requestException", ['request' => $ret]);
            return $ret;
        }
    }
}
