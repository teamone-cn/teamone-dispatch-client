# 霆万调度中心客户端(Teamone Dispatch Client)

## 项目简介

霆万调度中心客户端(Teamone Dispatch Client)是一个用于与调度系统进行交互的 PHP 组件库，由霆万技术团队自主研发。

霆万调度中心客户端提供了一个简单而强大的方式来与调度中心服务器进行通信，从而实现工作流的创建、调度、监控和管理。

## 主要特性

1. 简单易用：提供简洁的API，使得与Azkaban服务器进行通信变得轻松和直观。
2. 功能完备：支持创建、删除、调度、监控和管理工作流等功能。
3. 灵活性：可灵活地集成到各种PHP项目中，适用于不同的开发场景和需求。
4. 可扩展性：具有良好的可扩展性，可以根据需要进行定制和扩展。
5. 文档完善：提供详细的文档和示例，方便开发者快速上手和使用。

## 安装

````shell
composer require teamone/azkaban-client
````

## 接入指南

### 配置说明

````php
$config = [
    // Azkaban 服务器地址
    'host'            => 'Azkaban Server Host',
    // Azkaban 用户名称
    'username'        => 'username',
    // Azkaban 用户密码
    'password'        => 'password',
    // 连接超时时间(可选)
    'connect_timeout' => 60,
    // 请求超时时间(可选)
    'timeout'         => 60,
    // 调试模式(可选)
    'debug'           => true,
    // SSL 验证(可选)
    'verify'          => false,
    // 初始化压缩包(可选)
    'init_file'       => 'your-init-file/init.zip',
];
````

### 接入流程

````php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Teamone\Azkaban\Builder\GatewayBuilder;
use Teamone\Azkaban\Builder\GuzzleClientBuilder;
use Teamone\Azkaban\Builder\GuzzleClientBuilderImpl;

$config = [
    // Azkaban 服务器地址
    'host'     => 'https://192.168.60.80:8443',
    // Azkaban 用户名称
    'username' => 'azkaban',
    // Azkaban 用户密码
    'password' => 'azkaban',
];

// Guzzle Client 实现类
/** @var GuzzleClientBuilder $guzzleClientBuilder */
$guzzleClientBuilder = new GuzzleClientBuilderImpl($config);
$authGateway = GatewayBuilder::authGatewayBuilder($guzzleClientBuilder);

// 登录，获取 SessionId
$session     = $authGateway->login($config['username'], $config['password']);
assert(isset($session['session_id']), "Get SessionId Exception.");
$sessionId = $session['session_id'];

// 获取各个不同类型的客户端

// 项目
$projectGateway = GatewayBuilder::projectGatewayBuilder($guzzleClientBuilder);
// 流程
$flowGateway = GatewayBuilder::flowGatewayBuilder($guzzleClientBuilder);
// 任务
$jobGateway = GatewayBuilder::jobGatewayBuilder($guzzleClientBuilder);
// 执行器
$executorGateway = GatewayBuilder::executorGatewayBuilder($guzzleClientBuilder);

// 获取用户的所有项目
$projects = $projectGateway->fetchUserProjects($sessionId, $config['username']);
dump($projects);

// 项目名称
$project = "http-test";
// 流程名称
$flow = "basic";
// 任务名称
$job = "svip_request_token";

// 获取项目下的流程
$flows = $flowGateway->fetchProjectFlows($sessionId, $project);
dump($flows);

// 获取项目下的流程，流程下的任务
$job = $jobGateway->fetchJobInfo($sessionId, $project, $flow, $job);
dump($job);

// 执行流程
$executed = $executorGateway->executeFlow($sessionId, $project, $flow);
dump($executed);
````

### 创建 Project、Flow、Job 的流程

````php
// 项目名称
$project = "test-project-name";
// 项目描述
$description = "test-project-name-desc";
// 配置
$config = $guzzleClientBuilder->getConfig();
// 初始化项目附件
$initFile = __DIR__ . "/init.zip";
// flow 名称
$flow = "test-project-name-flow";
// job 名称
$job = "test-project-name-flow-job";

$trace = [];

// 0. 查看用户下的 project
$ret = $projectGateway->fetchUserProjects($this->sessionId, 'azkaban');

$trace['fetchUserProjects'] = $ret;
dump('fetchUserProjects');

// 1. 新增 project
$ret = $projectGateway->createProject($this->sessionId, $project, $description);

$trace['createProject'] = $ret;
dump('createProject');

// 2. 初始化 project
$ret = $projectGateway->initProject($this->sessionId, $project, $initFile);

$trace['initProject'] = $ret;
dump('initProject');

// 3. 添加 flow
$ret = $flowGateway->addFlowInCurrentProject($this->sessionId, $project, $flow);

$trace['addFlowInCurrentProject'] = $ret;
dump('addFlowInCurrentProject');

// 4. 添加 command job
$ret = $jobGateway->addJobInCurrentFlow($this->sessionId, $project, $flow, $job, "ls -alh /");

$trace['addJobInCurrentFlow'] = $ret;
dump('addJobInCurrentFlow');

// 5. 执行 command job
$ret = $executorGateway->executeFlow($this->sessionId, $project, $flow);

$trace['executeFlow'] = $ret;
dump('executeFlow');

// 6. 查看 project 下的 flow 下的 job 详情
$ret = $jobGateway->fetchJobInfo($this->sessionId, $project, $flow, $job);

$trace['fetchJobInfo'] = $ret;
dump('fetchJobInfo');

// 7. 查看 project 下的 flow
$ret = $flowGateway->fetchProjectFlows($this->sessionId, $project);

$trace['fetchProjectFlows'] = $ret;
dump('fetchProjectFlows');

// 8. 查询project下的flow下的job列表
$ret = $jobGateway->fetchFlowGraph($this->sessionId, $project, $flow);

$trace['fetchFlowGraph'] = $ret;
dump('fetchFlowGraph');

dump($trace);
````

## 定制化

````php
<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Exception\RequestException;
use Teamone\Azkaban\Builder\GuzzleClientBuilder;
use Teamone\Azkaban\ConvertUtil;
use Teamone\Azkaban\Exceptions\TeamoneAzkabanException;
use Teamone\Azkaban\Gateway\AuthGateway;
use Teamone\Azkaban\Gateway\GatewayRequest;
use Teamone\Azkaban\GuzzleClient;

class MyGuzzleClientBuilder implements GuzzleClientBuilder
{
    public function builder(): \Teamone\Azkaban\GuzzleClient
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
            throw new TeamoneAzkabanException($e->getMessage(), 400, $e);
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
````

## 单元测试

````shell
./vendor/bin/phpunit ./test/RequestTest.php --filter testLogin$
````
