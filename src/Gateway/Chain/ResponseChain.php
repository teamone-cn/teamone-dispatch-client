<?php

namespace Teamone\DispatchClient\Gateway\Chain;

use Psr\Http\Message\ResponseInterface;

interface ResponseChain
{
    public function next(ResponseChain $responseChain): ResponseChain;

    public function handle(ResponseInterface $response, array $contents = []): ?array;
}
