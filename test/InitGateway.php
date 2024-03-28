<?php

namespace Teamone\DispatchClientTest;

use Teamone\DispatchClient\Builder\GatewayBuilder;
use Teamone\DispatchClient\Builder\GuzzleClientBuilder;
use Teamone\DispatchClient\Builder\GuzzleClientBuilderImpl;
use Teamone\DispatchClient\Gateway\AuthGateway;
use Teamone\DispatchClient\Gateway\ExecutorGateway;
use Teamone\DispatchClient\Gateway\FlowGateway;
use Teamone\DispatchClient\Gateway\JobGateway;
use Teamone\DispatchClient\Gateway\ProjectGateway;

trait InitGateway
{
    protected $sessionId;
    protected $config;

    /**
     * @var GuzzleClientBuilder
     */
    protected $guzzleClientBuilder;

    /**
     * @var AuthGateway
     */
    protected $authGateway;

    /**
     * @var ProjectGateway
     */
    protected $projectGateway;

    /**
     * @var FlowGateway
     */
    protected $flowGateway;

    /**
     * @var JobGateway
     */
    protected $jobGateway;

    /**
     * @var ExecutorGateway
     */
    protected $executorGateway;

    public function init()
    {
        $this->config              = TestConfig::getConfig();
        /** @var GuzzleClientBuilder guzzleClientBuilder */
        $this->guzzleClientBuilder = new GuzzleClientBuilderImpl($this->config);
        $this->authGateway         = GatewayBuilder::authGatewayBuilder($this->guzzleClientBuilder);
        $this->sessionId           = $this->getSessionId();
        $this->projectGateway      = GatewayBuilder::projectGatewayBuilder($this->guzzleClientBuilder);
        $this->flowGateway         = GatewayBuilder::flowGatewayBuilder($this->guzzleClientBuilder);
        $this->jobGateway          = GatewayBuilder::jobGatewayBuilder($this->guzzleClientBuilder);
        $this->executorGateway     = GatewayBuilder::executorGatewayBuilder($this->guzzleClientBuilder);
    }

}
