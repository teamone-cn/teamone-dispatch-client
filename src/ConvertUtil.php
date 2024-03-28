<?php

namespace Teamone\DispatchClient;

use Teamone\DispatchClient\Exceptions\ConvertException;

class ConvertUtil
{
    /**
     * @desc 字符串转换为数组
     * @param string $contents
     * @return array
     */
    public static function toArray(string $contents): array
    {
        if (empty($contents)) {
            return [];
        }

        $contents = json_decode($contents, true);

        $error = json_last_error();

        if ($error > 0) {
            $msg = json_last_error_msg();
            throw new ConvertException("[String To Array Error]" . $msg, $error);
        }

        return $contents;
    }
}
