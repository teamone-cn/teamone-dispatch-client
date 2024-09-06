<?php

namespace Teamone\DispatchClient\Gateway;

interface JobGateway
{
    public function addJobInCurrentFlow(string $sessionId, string $project, string $flowName, string $jobName, string $command, string $dependOn = ""): array;

    public function addJobInCurrentFlowHttp(array $params): array;

    public function deleteJobsInCurrentFlow(string $sessionId, string $project, string $flowName, string $deleteJobNames): array;

    public function setJobOverrideProperty(string $sessionId, string $project, string $flowName, string $jobName, string $command): array;

    public function setJobOverridePropertyHttp(array $params): array;

    public function fetchFlowGraph(string $sessionId, string $project, string $flowName): array;

    public function fetchJobInfo(string $sessionId, string $project, string $flowName, string $jobName): array;

    public function fetchExecFlow(string $sessionId, int $execId): array;

    public function fetchExecJobLogs(string $sessionId, int $execId, string $jobId, int $offset, int $length): array;

    public function fetchFlowExecutions(string $sessionId, string $project, string $flow, int $start, int $length): array;
}
