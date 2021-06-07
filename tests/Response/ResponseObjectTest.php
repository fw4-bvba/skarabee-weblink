<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Tests\Response;

use Skarabee\Weblink\Response\ResponseObject;
use Skarabee\Weblink\Exception\InvalidPropertyException;
use PHPUnit\Framework\TestCase;
use DateTime;

class ResponseObjectTest extends TestCase
{
    public function testArrays(): void
    {
        $response = new ResponseObject(json_decode('{
            "foo": {
                "bar": [1, 2, 3]
            },
            "baz": {},
            "corge": {
                "grault": {
                    "garply": []
                }
            }
        }', false), [
            'foo',
            'baz',
            'qux',
            'corge' => [
                'grault',
            ],
        ]);

        $this->assertIsArray($response->foo);
        $this->assertIsArray($response->baz);
        $this->assertIsArray($response->qux);
        $this->assertIsArray($response->corge->grault);
    }

    public function testUnderscoreValue(): void
    {
        $response = new ResponseObject(json_decode('{
            "foo": {
                "_": "bar"
            }
        }', false));

        $this->assertEquals('bar', $response->foo->value);
    }

    public function testParseValue(): void
    {
        $response = new ResponseObject(json_decode('{
            "foo": [1, 2, 3],
            "bar": "UNDEFINED",
            "baz": -1,
            "qux": "TRUE",
            "corge": "FALSE",
            "grault": "2021-01-01 12:30:00"
        }', false));

        $this->assertIsArray($response->foo);
        $this->assertNull($response->bar);
        $this->assertNull($response->baz);
        $this->assertTrue($response->qux);
        $this->assertFalse($response->corge);
        $this->assertInstanceOf(DateTime::class, $response->grault);
    }

    public function testGetData(): void
    {
        $response = new ResponseObject(json_decode('{
            "foo": [1, 2, 3]
        }', false));

        $this->assertEquals([
            'foo' => [1, 2, 3],
        ], $response->getData());
    }

    public function testUnset(): void
    {
        $response = new ResponseObject(json_decode('{
            "foo": "bar"
        }', false));

        $this->assertTrue(isset($response->foo));

        unset($response->foo);

        $this->assertFalse(isset($response->foo));
    }

    public function testInvalidProperty(): void
    {
        $response = new ResponseObject(json_decode('{}', false));

        $this->expectException(InvalidPropertyException::class);
        $this->expectExceptionMessage('foo is not a valid property of Skarabee\Weblink\Response\ResponseObject');

        $foo = $response->foo;
    }

    public function testJsonEncode(): void
    {
        $response = new ResponseObject(json_decode('{
            "foo": "bar"
        }', false));

        $this->assertEquals('{"foo":"bar"}', json_encode($response));
    }
}
