<?php

namespace Teamone\DispatchClientTest;

class ProjectGatewayTest extends AuthGatewayTest
{
    public function testFetchUserProjects()
    {
        $ret = $this->projectGateway->fetchUserProjects($this->sessionId, $this->config["username"]);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testDeleteProject()
    {
        $ret = $this->projectGateway->deleteProject($this->sessionId, "test-project-03251613-v2");
        dump($ret);
        $this->assertTrue(true);
    }

    public function testProjectCreate()
    {
        $ret = $this->projectGateway->createProject($this->sessionId, "test-project-title-0320-1455", "test-project-description-0320-1455");
        dump($ret);
        $this->assertTrue(true);
    }

    public function testInitProject()
    {
        $ret = $this->projectGateway->initProject($this->sessionId, "test-project-title-0320-1455", __DIR__ . '/init.zip');
        dump($ret);
        $this->assertTrue(true);
    }

}
