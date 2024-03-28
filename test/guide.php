<?php

require __DIR__ . '/../vendor/autoload.php';

use Teamone\DispatchClientTest\Builder\GatewayBuilder;
use Teamone\DispatchClientTest\Builder\GuzzleClientBuilder;
use Teamone\DispatchClientTest\Builder\GuzzleClientBuilderImpl;

$config = [
    // Azkaban 服务器地址
    'host'     => 'https://192.168.60.80:8443',
    // Azkaban 用户名称
    'username' => 'azkaban',
    // Azkaban 用户密码
    'password' => 'teamone',
];

// Guzzle Client 实现类
/** @var GuzzleClientBuilder $guzzleClientBuilder */
$guzzleClientBuilder = new GuzzleClientBuilderImpl($config);

$authGateway = GatewayBuilder::authGatewayBuilder($guzzleClientBuilder);
$session     = $authGateway->login($config['username'], $config['password']);
assert(isset($session['session_id']), "Get SessionId Exception.");
$sessionId = $session['session_id'];

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

// 创建 Project、Flow、Job 的流程

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
