<?php

namespace Teamone\DispatchClient\Gateway;

interface ProjectGateway
{
    public function createProject(string $sessionId, string $name, string $description): array;

    public function initProject(string $sessionId, string $project, string $filepath): array;

    public function fetchUserProjects(string $sessionId, string $user): array;

    public function deleteProject(string $sessionId, string $project): array;
}
