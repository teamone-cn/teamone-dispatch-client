<?php

namespace Teamone\DispatchClient\Gateway\Chain;

use Psr\Http\Message\ResponseInterface;
use Teamone\DispatchClient\Exceptions\ChainException;

/**
 * 响应类型检测链
 */
class ContentTypeResponseChain extends AbstractResponseChain
{
    protected $allowContentTypes;

    public function __construct(array $allowContentTypes = ['application/json'])
    {
        $this->allowContentTypes = $allowContentTypes;
    }

    public function handle(ResponseInterface $response, array $contents = []): ?array
    {
        $contentType = $response->getHeader('Content-type');

        if (!empty($contentType) && $firstContentType = current($contentType)) {
            if ($firstContentType !== 'application/json') {
                throw new ChainException("Content type, {$firstContentType}");
            }
        }

        return parent::handle($response, $contents);
    }
}
