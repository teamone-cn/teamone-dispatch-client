<?php

namespace Teamone\DispatchClient\Gateway;

interface ExecutorGateway
{
    public function executeFlow(string $sessionId, string $project, string $flow): array;

    public function cancelFlow(string $sessionId, int $execId): array;

    public function scheduleCronFlow(string $sessionId, string $projectName, string $flow, string $cronExpression, array $disabled): array;

    public function executeFlowJobs(string $sessionId, string $project, string $flow, string $jobIds): array;

    public function cancelFlowJobs(string $sessionId, string $project, string $flow, string $jobs, int $execId): array;

    public function pauseFlow(string $sessionId, int $execId): array;

    public function resumeFlow(string $sessionId, int $execId): array;
}
