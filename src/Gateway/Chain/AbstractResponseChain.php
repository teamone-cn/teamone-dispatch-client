<?php

namespace Teamone\DispatchClient\Gateway\Chain;

use Psr\Http\Message\ResponseInterface;

abstract class AbstractResponseChain implements ResponseChain
{
    /**
     * @var ResponseChain
     */
    protected $nextResponseChain;

    public function next(ResponseChain $responseChain): ResponseChain
    {
        $this->nextResponseChain = $responseChain;

        return $responseChain;
    }

    public function handle(ResponseInterface $response, array $contents = []): ?array
    {
        if (!is_null($this->nextResponseChain)) {
            return $this->nextResponseChain->handle($response, $contents);
        }

        return $contents;
    }

    public static function link(ResponseChain $firstChain, ResponseChain ...$chains): ResponseChain
    {
        $headChain = $firstChain;
        foreach ($chains as $chain) {
            $headChain->next($chain);
            $headChain = $chain;
        }
        return $firstChain;
    }
}
