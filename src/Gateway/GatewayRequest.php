<?php

namespace Teamone\DispatchClient\Gateway;


use Teamone\DispatchClient\Builder\GuzzleClientBuilder;
use Teamone\DispatchClient\GuzzleClient;

abstract class GatewayRequest
{
    /**
     * @var GuzzleClient
     */
    private $guzzleClientBuilder;

    public function __construct(GuzzleClientBuilder $guzzleClientBuilder)
    {
        $this->guzzleClientBuilder = $guzzleClientBuilder;
    }

    public function headers(): array
    {
        return [
            "Content-Type" => "application/x-www-form-urlencoded",
        ];
    }

    public function getGuzzleClient(): GuzzleClient
    {
        $client = $this->guzzleClientBuilder->builder();

        $headers = $this->headers();

        if (!empty($headers)) {
            $client->headers($headers);
        }

        return $client;
    }
}
