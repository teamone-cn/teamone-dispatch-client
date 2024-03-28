<?php

namespace Teamone\DispatchClientTest;

class FlowGatewayTest extends AuthGatewayTest
{
    public function testAddFlowInCurrentProject()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow-v2";
        $ret     = $this->flowGateway->addFlowInCurrentProject($this->sessionId, $project, $flow);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testDeleteFlowInCurrentProject()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow-v2";

        $ret = $this->flowGateway->deleteFlowInCurrentProject($this->sessionId, $project, $flow);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testFetchProjectFlows()
    {
        $project = "test-project-0326-1354";

        $ret = $this->flowGateway->fetchProjectFlows($this->sessionId, $project);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testGetRunningExecutor()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow";
        $ret     = $this->flowGateway->getRunningExecutor($this->sessionId, $project, $flow);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testFetchFlowExecutions()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow-v2";
        $ret     = $this->flowGateway->fetchFlowExecutions($this->sessionId, $project, $flow);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testFetchSchedule()
    {
        $projectId = 13;
        $flowId    = "test-project-0326-1354-flow";
        $ret       = $this->flowGateway->fetchSchedule($this->sessionId, $projectId, $flowId);
        dump($ret);
        $this->assertTrue(true);
    }

}
