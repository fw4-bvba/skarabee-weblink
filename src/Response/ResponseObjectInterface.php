<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Response;

interface ResponseObjectInterface
{
    /**
     * @param object $data Unparsed response data
     * @param array<mixed> $arrays Array indicating which properties should be
     * converted to arrays. If the array value is a string, it is interpreted as
     * the name of a property to convert to an array. If the value is an array
     * with a string as key, the key is interpreted as the name of a property
     * containing an object, while the array value indicates which properties of
     * said object should be converted to an array. This applies recursively.
     */
    public function __construct(object $data, array $arrays);
}
