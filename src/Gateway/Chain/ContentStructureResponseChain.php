<?php

namespace Teamone\DispatchClient\Gateway\Chain;

use Psr\Http\Message\ResponseInterface;
use Teamone\DispatchClient\Exceptions\ChainException;

/**
 * 字段检测链
 */
class ContentStructureResponseChain extends AbstractResponseChain
{
    // 字段列表
    private $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function handle(ResponseInterface $response, array $contents = []): ?array
    {
        if (!empty($contents)) {
            foreach ($this->fields as $field) {
                if (!empty($field) && array_key_exists($field, $contents)) {
                    continue;
                }
                throw new ChainException("Content Structure, Missing Return Key : {$field}.");
            }
        }

        return parent::handle($response, $contents);
    }
}
