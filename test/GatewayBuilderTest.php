<?php

namespace Teamone\DispatchClientTest;

class GatewayBuilderTest extends AuthGatewayTest
{

    public function testExecuteFlow()
    {
        $ret = $this->executorGateway->executeFlow($this->getSessionId(), 1, '');
        dump($ret);
        $this->assertTrue(true);
    }

    public function testCommand()
    {
        $projectGateway  = $this->projectGateway;
        $flowGateway     = $this->flowGateway;
        $jobGateway      = $this->jobGateway;
        $executorGateway = $this->executorGateway;

        // 项目名称
        $project = "test-project-0326-1354";
        // 项目描述
        $description = "{$project}-desc";
        // 配置
        $config = $this->guzzleClientBuilder->getConfig();
        // 初始化项目附件
        $initFile = $config['init_file'] ?? __DIR__ . "/init.zip";
        // flow 名称
        $flow = "{$project}-flow";
        // job 名称
        $job = "{$project}-flow-job";

        $trace = [];

        // 0. 查看用户下的 project
        $ret                        = $projectGateway->fetchUserProjects($this->sessionId, 'azkaban');
        $trace['fetchUserProjects'] = $ret;
        dump('fetchUserProjects');

        // 1. 新增 project
        $ret                    = $projectGateway->createProject($this->sessionId, $project, $description);
        $trace['createProject'] = $ret;
        dump('createProject');

        // 2. 初始化 project
        $ret                  = $projectGateway->initProject($this->sessionId, $project, $initFile);
        $trace['initProject'] = $ret;
        dump('initProject');

        // 3. 添加 flow
        $ret                              = $flowGateway->addFlowInCurrentProject($this->sessionId, $project, $flow);
        $trace['addFlowInCurrentProject'] = $ret;
        dump('addFlowInCurrentProject');

        // 4. 添加 command job
        $ret                          = $jobGateway->addJobInCurrentFlow($this->sessionId, $project, $flow, $job, "ls -alh /");
        $trace['addJobInCurrentFlow'] = $ret;
        dump('addJobInCurrentFlow');

        // 5. 执行 command job
        $ret                  = $executorGateway->executeFlow($this->sessionId, $project, $flow);
        $trace['executeFlow'] = $ret;
        dump('executeFlow');

        // 6. 查看 project 下的 flow 下的 job 详情
        $ret                   = $jobGateway->fetchJobInfo($this->sessionId, $project, $flow, $job);
        $trace['fetchJobInfo'] = $ret;
        dump('fetchJobInfo');

        // 7. 查看 project 下的 flow
        $ret                        = $flowGateway->fetchProjectFlows($this->sessionId, $project);
        $trace['fetchProjectFlows'] = $ret;
        dump('fetchProjectFlows');

        // 8. 查询project下的flow下的job列表
        $ret                     = $jobGateway->fetchFlowGraph($this->sessionId, $project, $flow);
        $trace['fetchFlowGraph'] = $ret;
        dump('fetchFlowGraph');

        $this->appendRet($trace);

        $this->assertTrue(true);
    }

    public function appendRet(array $ret)
    {
        dump($ret);
        file_put_contents(__DIR__ . '/runner.json', json_encode($ret, JSON_UNESCAPED_UNICODE), FILE_APPEND);
    }
}
