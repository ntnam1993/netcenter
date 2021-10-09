<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;

abstract class Service
{
    protected $client;
    protected $response;

    public function __construct()
    {
        $config = array_merge(['base_uri' => $this->uri()], $this->options(), ['timeout' => config('common.API_TIMEOUT')], ['curl'    => [CURLOPT_NOSIGNAL => true]]);

        $this->client = app(Client::class, $config);
    }

    public function options()
    {
        return [
            'headers' => [
                'Accept' => 'application/xml'
            ]
        ];
    }

    abstract public function uri();

    public function getBodyAsync($type = 'object')
    {
        if ($this->response instanceof Promise) {
            $this->response = $this->response->wait();
        }
        return $this->getBody($type);
    }

    public function getBody($type = 'object')
    {
        return $this->parse($this->response, $type);
    }

    public function parse($response, $type = 'object')
    {
        if ($response->getStatusCode() == 200) {
            if ('object' == $type) {
                return json_decode($response->getBody()->getContents());
            }
            return $response->getBody()->getContents();
        }
        return null;
    }
}
