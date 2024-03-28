<?php

namespace Teamone\DispatchClient\Gateway\Chain;

use Psr\Http\Message\ResponseInterface;
use Teamone\DispatchClient\Exceptions\ChainException;

/**
 * 错误字段响应链
 */
class ContentErrorResponseChain extends AbstractResponseChain
{
    public function handle(ResponseInterface $response, array $contents = []): ?array
    {
        if (isset($contents['error']) && !empty($contents['error'])) {
            throw new ChainException("Content Error, " . ($contents['error'] ?? 'Unknown'));
        }

        return parent::handle($response, $contents);
    }
}
