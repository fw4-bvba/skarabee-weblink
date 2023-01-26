<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Response;

class Response extends ResponseObject
{
    /**
     * Parse data as an array of instances of this response class.
     *
     * @param mixed $data
     *
     * @return array<static>
     */
    public static function collection($data): array
    {
        $response = [];
        if (!is_array($data)) {
            $data = (self::getFirstPropertyOfObject($data) ?? false) ?: [];
        }
        foreach ($data as $value) {
            $response[] = new static($value);
        }
        return $response;
    }
}
