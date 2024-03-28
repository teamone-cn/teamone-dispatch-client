<?php

namespace Teamone\DispatchClientTest;

class TestConfig
{
    public static function getConfig(): array
    {
        return [
            'host'            => 'https://192.168.60.80:8443',
            'username'        => 'azkaban',
            'password'        => 'teamone',
            'connect_timeout' => 60,
            'timeout'         => 60,
            'debug'           => true,
            'verify'          => false,
        ];
    }
}
