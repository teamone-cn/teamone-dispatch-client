<?php

namespace Teamone\DispatchClientTest;


class ExecutorGatewayTest extends AuthGatewayTest
{
    public function testExecuteFlow()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow-v2";
        $ret     = $this->executorGateway->executeFlow($this->sessionId, $project, $flow);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testCancelFlow()
    {
        $execId = 219;
        $ret    = $this->executorGateway->cancelFlow($this->sessionId, $execId);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testScheduleCronFlow()
    {
        $project        = "test-project-0326-1354";
        $flow           = "test-project-0326-1354-flow-v2";
        // 每分钟执行一次
        $cronExpression = "0 * * ? * *";
        // 排除执行
        $disabled = ["test-project-0326-1354-flow-v2-job-v1", "test-project-0326-1354-flow-v2-job-v2"];

        $ret = $this->executorGateway->scheduleCronFlow($this->sessionId, $project, $flow, $cronExpression, $disabled);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testExecuteFlowJobs()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow-v2";
        $job     = "test-project-0326-1354-flow-v2-job-v3";
        $ret     = $this->executorGateway->executeFlowJobs($this->sessionId, $project, $flow, $job);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testCancelFlowJobs()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow";
        $job     = "test-project-0326-1354-flow-job";
        $execId  = 1;
        $ret     = $this->executorGateway->cancelFlowJobs($this->sessionId, $project, $flow, $job, $execId);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testPauseFlow()
    {
        $execId = 219;
        $ret    = $this->executorGateway->pauseFlow($this->sessionId, $execId);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testResumeFlow()
    {
        $execId = 219;
        $ret    = $this->executorGateway->resumeFlow($this->sessionId, $execId);
        dump($ret);
        $this->assertTrue(true);
    }
}
