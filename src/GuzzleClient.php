<?php

namespace Teamone\DispatchClient;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GuzzleClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $clientConfig;

    public function __construct(array $clientConfig = [])
    {
        // 这是当前类的配置
        $this->config = [];
        // 这是 Client 类的配置
        $this->clientConfig = $clientConfig;
        $this->client       = new Client($clientConfig);
    }

    /**
     * TeamOne 请求的重定向行为
     * @param array|bool $value
     * @return $this
     */
    public function allowRedirects($value = []): GuzzleClient
    {
        if (is_bool($value)) {
            $this->config['allow_redirects'] = $value;
        } else {
            $this->config['allow_redirects'] = [
                // max: (int, 默认为5) 允许重定向次数的最大值
                'max'             => (int)($value['max'] ?? 5),
                // strict: (bool, 默认为 false) 设置成 true 使用严格模式重定向
                'strict'          => (bool)($value['strict'] ?? false),
                // referer: (bool, 默认为 true) 设置成 false 重定向时禁止添加Refer头信息
                'referer'         => (bool)($value['referer'] ?? true),
                // protocols: (array, 默认为['http', 'https']) 指定允许重定向的协议
                'protocols'       => (array)($value['protocols'] ?? ['http', 'https']),
                // rack_redirects: (bool) 当设置成 true 时，每个重定向的URI将会按序被跟踪头信息 X-Guzzle-Redirect-History
                'track_redirects' => (bool)($value['track_redirects'] ?? true),
            ];
        }

        return $this;
    }

    /**
     * @desc HTTP认证
     * @param array $value
     * @return $this
     */
    public function auth(array $value): GuzzleClient
    {
        if (isset($value['username']) && isset($value['password'])) {
            $this->config['auth'] = [$value['username'], $value['password']];
        }
        return $this;
    }

    /**
     * @desc body 选项用来控制一个请求(比如：PUT, POST, PATCH)的主体部分
     * @param string|StreamInterface $value
     * @return $this
     */
    public function body($value): GuzzleClient
    {
        $this->config['body'] = $value;
        return $this;
    }

    /**
     * @desc 设置成指定PEM格式认证文件的路径的字符串，如果需要密码，需要设置成一个数组，其中PEM文件在第一个元素，密码在第二个元素
     * @param array $value
     * @return $this
     */
    public function cert(array $value): GuzzleClient
    {
        if (isset($value['pem']) && isset($value['password'])) {
            $this->config['cert'] = [$value['pem'], $value['password']];
        } else if (isset($value['pem'])) {
            $this->config['cert'] = [$value['pem']];
        }
        return $this;
    }

    /**
     * @desc 声明是否在请求中使用cookie，或者要使用的cookie jar，或者要发送的cookie
     * @param CookieJar $cookieJar
     * @return $this
     */
    public function cookies(CookieJar $cookieJar): GuzzleClient
    {
        $this->config['cookies'] = $cookieJar;
        return $this;
    }

    /**
     * @desc 表示等待服务器响应超时的最大值，使用 0 将无限等待 (默认行为)
     * @param int $value
     * @return $this
     */
    public function connectTimeout(int $value): GuzzleClient
    {
        $this->config['connect_timeout'] = $value;
        return $this;
    }

    // 表示等待服务器响应超时的最大值，使用 0 将无限等待 (默认行为).
    public function debug(bool $value): GuzzleClient
    {
        $this->config['debug'] = $value;
        return $this;
    }

    /**
     * @desc 表示等待服务器响应超时的最大值，使用 0 将无限等待 (默认行为)
     * @param bool|string $value
     * @return $this
     */
    public function decodeContent($value): GuzzleClient
    {
        $this->config['decode_content'] = $value;
        return $this;
    }

    /**
     * @desc 发送请求前延迟的毫秒数值
     * @param int|float $value
     * @return $this
     */
    public function delay($value): GuzzleClient
    {
        $this->config['delay'] = $value;
        return $this;
    }

    /**
     * @desc 控制"Expect: 100-Continue"报文头的行为
     * @param int|bool $value
     * @return $this
     */
    public function expect($value): GuzzleClient
    {
        $this->config['expect'] = $value;
        return $this;
    }

    /**
     * @desc 用来发送一个 application/x-www-form-urlencoded POST请求
     * @param array $value
     * @return $this
     */
    public function formParams($value): GuzzleClient
    {
        $this->config['form_params'] = $value;
        return $this;
    }

    /**
     * @desc 要添加到请求的报文头的关联数组
     * @param array $value
     * @return $this
     */
    public function headers(array $value): GuzzleClient
    {
        $this->config['headers'] = $value;
        return $this;
    }

    /**
     * @desc 设置成 false 来禁用HTTP协议抛出的异常(如 4xx 和 5xx 响应)，默认情况下HTPP协议出错时会抛出异常
     * @param bool $value
     * @return $this
     */
    public function httpErrors(bool $value): GuzzleClient
    {
        $this->config['http_errors'] = $value;
        return $this;
    }

    /**
     * @desc json 选项用来轻松将JSON数据当成主体上传， 如果没有设置Content-Type头信息的时候会设置成 application/json
     * @param array $value
     * @return $this
     */
    public function json(array $value): GuzzleClient
    {
        $this->config['json'] = empty($value) ? new \stdClass() : $value;

        return $this;
    }

    /**
     * @desc 设置请求的主体为 multipart/form-data 表单
     * @param array $value
     * @return $this
     */
    public function multipart(array $value): GuzzleClient
    {
        $this->config['multipart'] = $value;
        return $this;
    }

    /**
     * @desc 回调函数，当响应的HTTP头信息被接收且主体部分还未开始下载的时候调用
     * @param callable $value
     * @return $this
     */
    public function onHeaders(callable $value): GuzzleClient
    {
        $this->config['on_headers'] = $value;
        return $this;
    }

    /**
     * @desc 传入字符串来指定HTTP代理，或者为不同代理指定不同协议的数组
     * @param array $value
     * @return $this
     */
    public function proxy(array $value): GuzzleClient
    {
        $this->config['proxy'] = $value;
        return $this;
    }

    /**
     * @desc 要添加到请求的查询字符串的关联数组或查询字符串
     * @param array $value
     * @return $this
     */
    public function query(array $value): GuzzleClient
    {
        $this->config['query'] = $value;
        return $this;
    }

    /**
     * @desc 声明响应的主体部分将要保存的位置
     * @param StreamInterface $value
     * @return $this
     */
    public function sink(StreamInterface $value): GuzzleClient
    {
        $this->config['sink'] = $value;
        return $this;
    }

    /**
     * @desc 指定一个链接到私有SSL密钥的PEM格式的文件的路径的字符串
     * 如果需要密码，设置成一个数组，数组第一个元素为链接到私有SSL密钥的PEM格式的文件的路径，第二个元素为认证密码
     * @param string|array $value
     * @return $this
     */
    public function ssl_key($value): GuzzleClient
    {
        $this->config['ssl_key'] = $value;
        return $this;
    }

    /**
     * @desc 声明响应的主体部分将要保存的位置
     * @param bool $value
     * @return $this
     */
    public function synchronous(bool $value): GuzzleClient
    {
        $this->config['synchronous'] = $value;
        return $this;
    }

    /**
     * @desc 声明响应的主体部分将要保存的位置
     * @param bool|string $value
     * @return $this
     */
    public function verify($value): GuzzleClient
    {
        $this->config['verify'] = $value;
        return $this;
    }

    /**
     * @desc 请求超时的秒数使用 0 无限期的等待(默认行为)
     * @param float $value
     * @return $this
     */
    public function timeout(float $value): GuzzleClient
    {
        $this->config['timeout'] = $value;
        return $this;
    }

    /**
     * @desc 请求要使用到的协议版本
     * @param string|float $value
     * @return $this
     */
    public function version($value): GuzzleClient
    {
        $this->config['version'] = $value;
        return $this;
    }

    public function get(string $uri): ResponseInterface
    {
        return $this->client->get($uri, $this->config);
    }

    public function head(string $uri): ResponseInterface
    {
        return $this->client->head($uri, $this->config);
    }

    public function put(string $uri): ResponseInterface
    {
        return $this->client->put($uri, $this->config);
    }

    public function post(string $uri): ResponseInterface
    {
        return $this->client->post($uri, $this->config);
    }

    public function patch(string $uri): ResponseInterface
    {
        return $this->client->patch($uri, $this->config);
    }

    public function delete(string $uri): ResponseInterface
    {
        return $this->client->delete($uri, $this->config);
    }
}
