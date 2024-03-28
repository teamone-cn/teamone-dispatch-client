<?php

namespace Teamone\DispatchClient\Builder;

use Teamone\DispatchClient\Gateway\AuthGateway;
use Teamone\DispatchClient\Gateway\ExecutorGateway;
use Teamone\DispatchClient\Gateway\FlowGateway;
use Teamone\DispatchClient\Gateway\Impl\AuthGatewayImpl;
use Teamone\DispatchClient\Gateway\Impl\ExecutorGatewayImpl;
use Teamone\DispatchClient\Gateway\Impl\FlowGatewayImpl;
use Teamone\DispatchClient\Gateway\Impl\JobGatewayImpl;
use Teamone\DispatchClient\Gateway\Impl\ProjectGatewayImpl;
use Teamone\DispatchClient\Gateway\JobGateway;
use Teamone\DispatchClient\Gateway\ProjectGateway;

class GatewayBuilder
{
    public static function authGatewayBuilder(GuzzleClientBuilder $guzzleClientBuilder): AuthGateway
    {
        return new AuthGatewayImpl($guzzleClientBuilder);
    }

    public static function executorGatewayBuilder(GuzzleClientBuilder $guzzleClientBuilder): ExecutorGateway
    {
        return new ExecutorGatewayImpl($guzzleClientBuilder);
    }

    public static function projectGatewayBuilder(GuzzleClientBuilder $guzzleClientBuilder): ProjectGateway
    {
        return new ProjectGatewayImpl($guzzleClientBuilder);
    }

    public static function flowGatewayBuilder(GuzzleClientBuilder $guzzleClientBuilder): FlowGateway
    {
        return new FlowGatewayImpl($guzzleClientBuilder);
    }

    public static function jobGatewayBuilder(GuzzleClientBuilder $guzzleClientBuilder): JobGateway
    {
        return new JobGatewayImpl($guzzleClientBuilder);
    }
}
