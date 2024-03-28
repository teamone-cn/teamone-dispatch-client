<?php

namespace Teamone\DispatchClient\Gateway\Impl;

use GuzzleHttp\Exception\RequestException;
use Teamone\DispatchClient\Exceptions\InvalidArgumentException;
use Teamone\DispatchClient\Exceptions\TeamoneDispatchClientException;
use Teamone\DispatchClient\Gateway\AuthGateway;
use Teamone\DispatchClient\Gateway\Chain\AbstractResponseChain;
use Teamone\DispatchClient\Gateway\Chain\ContentErrorResponseChain;
use Teamone\DispatchClient\Gateway\Chain\ContentStatusResponseChain;
use Teamone\DispatchClient\Gateway\Chain\ContentStructureResponseChain;
use Teamone\DispatchClient\Gateway\Chain\ContentTypeResponseChain;
use Teamone\DispatchClient\Gateway\Chain\ConvertArrayResponseChain;
use Teamone\DispatchClient\Gateway\Chain\HttpStatusResponseChain;
use Teamone\DispatchClient\Gateway\GatewayRequest;

class AuthGatewayImpl extends GatewayRequest implements AuthGateway
{
    /**
     * @desc 用户登录接口
     * @param string $username 账号
     * @param string $password 密码
     * @return array
     */
    public function login(string $username, string $password): array
    {
        $valid = true;
        $json  = [
            'username' => $username ?? ($valid = null),
            'password' => $password ?? ($valid = null),
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/?action=login';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = ['session.id', 'status',];

            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
                // 判断返回类型
                new ContentTypeResponseChain(),
                // 转换类型
                new ConvertArrayResponseChain(),
                // status=error
                new ContentStatusResponseChain(),
                // 含有 error 字段
                new ContentErrorResponseChain(),
                // 返回结构
                new ContentStructureResponseChain($fields),
            )->handle($response);

            $ret = [
                'session_id' => $contents['session.id'],
                'status'     => $contents['status'],
            ];

            return $ret;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }

    }
}
