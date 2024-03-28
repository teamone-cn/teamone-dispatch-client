<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Exception\RequestException;
use Teamone\DispatchClient\Builder\GuzzleClientBuilder;
use Teamone\DispatchClient\ConvertUtil;
use Teamone\DispatchClient\Exceptions\TeamoneDispatchClientException;
use Teamone\DispatchClient\Gateway\AuthGateway;
use Teamone\DispatchClient\Gateway\GatewayRequest;
use Teamone\DispatchClient\GuzzleClient;

class MyGuzzleClientBuilder implements GuzzleClientBuilder
{
    public function builder(): GuzzleClient
    {
        $config = $this->getConfig();

        // 全局初始化参数
        return (new GuzzleClient(['base_uri' => $config['host']]))->verify($config['verify']);
    }

    public function getConfig(): array
    {
        return [
            // Azkaban 服务器地址
            'host'     => 'https://192.168.60.80:8443',
            // Azkaban 用户名称
            'username' => 'azkaban',
            // Azkaban 用户密码
            'password' => 'teamone',
            // SSL 验证
            'verify'   => false,
        ];
    }

}

class MyAuthGateway extends GatewayRequest implements AuthGateway
{
    public function login(string $username, string $password): array
    {
        $json = [
            'username' => $username,
            'password' => $password,
        ];
        try {
            $uri      = '/?action=login';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);

            return ConvertUtil::toArray($response->getBody()->getContents());
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }
}


// Guzzle Client 实现类
/** @var GuzzleClientBuilder $guzzleClientBuilder */
$guzzleClientBuilder = new MyGuzzleClientBuilder();
$authGateway         = new MyAuthGateway($guzzleClientBuilder);
$config              = $guzzleClientBuilder->getConfig();
$session             = $authGateway->login($config['username'], $config['password']);
dump($session);
