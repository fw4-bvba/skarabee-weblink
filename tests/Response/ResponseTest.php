<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Tests\Response;

use PHPUnit\Framework\TestCase;
use Skarabee\Weblink\Response\Response;

class ResponseTest extends TestCase
{
    public function testCollection(): void
    {
        $response = json_decode('{
            "foo": [
                {"bar": 1},
                {"bar": 2},
                {"bar": 3}
            ]
        }', false);
        $collection = Response::collection($response);
        $this->assertIsArray($collection);
        $this->assertCount(3, $collection);

        $response = json_decode('[
            {"bar": 1},
            {"bar": 2},
            {"bar": 3}
        ]', false);
        $collection = Response::collection($response);
        $this->assertIsArray($collection);
        $this->assertCount(3, $collection);

        $response = json_decode('{
            "foo": []
        }', false);
        $collection = Response::collection($response);
        $this->assertIsArray($collection);
        $this->assertCount(0, $collection);

        $response = json_decode('{}', false);
        $collection = Response::collection($response);
        $this->assertIsArray($collection);
        $this->assertCount(0, $collection);
    }
}
