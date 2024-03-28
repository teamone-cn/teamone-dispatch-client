<?php

namespace Teamone\DispatchClient\Gateway;

interface FlowGateway
{
    public function addFlowInCurrentProject(string $sessionId, string $project, string $flowName): array;

    public function deleteFlowInCurrentProject(string $sessionId, string $project, string $flowName): array;

    public function fetchProjectFlows(string $sessionId, string $project): array;

    public function getRunningExecutor(string $sessionId, string $project, string $flowName): array;

    public function fetchFlowExecutions(string $sessionId, string $project, string $flow, int $start, int $length): array;

    public function fetchSchedule(string $sessionId, int $projectId, string $flowId): array;
}
