<?php

namespace Teamone\DispatchClient\Gateway;

interface AuthGateway
{
    /**
     * @desc 登录
     * @param string $username 用户
     * @param string $password 密码
     * @return array
     */
    public function login(string $username, string $password): array;
}
