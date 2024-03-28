<?php

namespace Teamone\DispatchClient\Builder;

use Teamone\DispatchClient\GuzzleClient;

class GuzzleClientBuilderImpl implements GuzzleClientBuilder
{
    /**
     * @var array
     */
    private $config = [
        'host'            => '',
        'username'        => '',
        'password'        => '',
        'connect_timeout' => 60,
        'timeout'         => 60,
        'debug'           => true,
        'verify'          => false,
        'init_file'       => __DIR__ . "/resource/init.zip",
    ];

    public function __construct(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    public function builder(): GuzzleClient
    {
        $guzzleClient = new GuzzleClient(['base_uri' => $this->config['host']]);

        // 全局初始化参数
        $guzzleClient->allowRedirects()
            ->connectTimeout($this->config['connect_timeout'])
            ->debug($this->config['debug'])
            ->timeout($this->config['timeout'])
            ->verify($this->config['verify']);

        return $guzzleClient;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
