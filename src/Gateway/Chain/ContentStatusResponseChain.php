<?php

namespace Teamone\DispatchClient\Gateway\Chain;

use Psr\Http\Message\ResponseInterface;
use Teamone\DispatchClient\Exceptions\ChainException;

/**
 * 状态字段响应链
 */
class ContentStatusResponseChain extends AbstractResponseChain
{
    public function handle(ResponseInterface $response, array $contents = []): ?array
    {
        if (isset($contents['status']) && $contents['status'] === 'error') {
            throw new ChainException("Content Status, " . ($contents['message'] ?? 'Unknown'));
        }

        return parent::handle($response, $contents);
    }
}
