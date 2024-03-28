<?php

namespace Teamone\DispatchClient\Gateway\Chain;

use Psr\Http\Message\ResponseInterface;
use Teamone\DispatchClient\ConvertUtil;
use Teamone\DispatchClient\Exceptions\ChainException;
use Teamone\DispatchClient\Exceptions\ConvertException;

/**
 * 转换链，字符串转换为数组
 */
class ConvertArrayResponseChain extends AbstractResponseChain
{
    public function handle(ResponseInterface $response, array $contents = []): ?array
    {
        $contentsMixed = $response->getBody()->getContents();

        try {
            $contentsMixed = ConvertUtil::toArray($contentsMixed);
        } catch (ConvertException $e) {
            throw new ChainException($e->getMessage(), 400, $e);
        }

        return parent::handle($response, $contentsMixed);
    }
}
