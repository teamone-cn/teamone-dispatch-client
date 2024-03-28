<?php

namespace Teamone\DispatchClient\Gateway\Chain;

use Psr\Http\Message\ResponseInterface;
use Teamone\DispatchClient\Exceptions\ChainException;

/**
 * 响应状态码检测链
 */
class HttpStatusResponseChain extends AbstractResponseChain
{
    public const OK = 200;

    // 字段列表
    private $allowStatus;

    public function __construct(array $allowStatus = [])
    {
        $this->allowStatus = $allowStatus;
    }

    public function handle(ResponseInterface $response, array $contents = []): ?array
    {
        $statusCode = $response->getStatusCode();

        if (!empty($this->allowStatus)) {
            if (!in_array($statusCode, $this->allowStatus)) {
                throw new ChainException("Error status code: {$statusCode}.");
            }
        }

        return parent::handle($response, $contents);
    }
}
