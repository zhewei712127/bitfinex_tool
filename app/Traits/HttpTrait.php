<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

trait HttpTrait
{
    /**
     * @param $method
     * @param $args
     * @param $url
     * @param bool $do_json_decode
     * @return mixed
     * @throws GuzzleException
     */
    public function base($method, $args, $url, $do_json_decode = true)
    {
        /** @var Client $cli */
        $cli = app(Client::class);

        /** @var Response $res */
        $res = $cli->request($method, $url, $args);

        if ($do_json_decode) {
            $body = json_decode($res->getBody()->getContents(), true);
        } else {
            $body = $res->getBody()->getContents();
        }

        return $body;
    }

    /**
     * @param $args
     * @param $url
     * @param bool $check
     * @return mixed
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get($args, $url, $do_json_encode = true)
    {
        if ($do_json_encode) {
            $args['headers']['Content-Type'] = 'application/json';
        }

        $data = $this->base('GET', $args, $url);

        return $data;
    }

    /**
     * @param $args
     * @param $url
     * @param $do_json_encode
     * @param $check
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws GuzzleException
     * @throws ApiException
     */
    public function post($args, $url, $do_json_encode = true)
    {
        if ($do_json_encode) {
            $args['headers']['Content-Type'] = 'application/json';
            $args['body'] = json_encode($args['body']);
        }

        $data = $this->base('POST', $args, $url, $do_json_encode);

        return $data;
    }
}
