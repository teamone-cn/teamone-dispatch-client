<?php

namespace Teamone\DispatchClientTest;

use PHPUnit\Framework\TestCase;

class AuthGatewayTest extends TestCase
{
    use InitGateway;

    protected function setUp(): void
    {
        $this->init();
    }

    public function testLogin()
    {
        $ret = $this->cacheSession();
        dump($ret);
        $this->assertTrue(true);
    }

    public function getSessionId(): string
    {
        return $this->cacheSession()['session_id'] ?? "";
    }

    protected function cacheSession(): array
    {
        $filename = __DIR__ . "/session.cache";

        $loginCallback = function () use ($filename): array {
            $ret         = $this->authGateway->login($this->config['username'], $this->config['password']);
            $ret["time"] = time();
            file_put_contents($filename, json_encode($ret, JSON_UNESCAPED_UNICODE));
            return $ret;
        };

        $json = [];
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            if (!empty($content)) {
                $json = json_decode($content, true);
                if (json_last_error()) {
                    throw new \Exception(json_last_error_msg());
                }
            }
        }

        $cacheTime = 3600;
        if (!empty($json) && isset($json["time"])) {
            //
            if ((time() - (int)$json["time"]) > $cacheTime) {
                return $loginCallback();
            }

            return $json;
        }

        return $loginCallback();
    }
}
