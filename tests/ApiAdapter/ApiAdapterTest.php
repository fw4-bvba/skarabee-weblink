<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Tests\ApiAdapter;

use Skarabee\Weblink\Tests\ApiTestCase;
use Skarabee\Weblink\Request\Request;
use Skarabee\Weblink\Exception\WeblinkException;

class ApiAdapterTest extends ApiTestCase
{
    public function testRequestMissingData(): void
    {
        $request = new Request('foo');
        $this->queueResponse('{}');

        $this->expectException(WeblinkException::class);
        $this->expectExceptionMessage('Response from Skarabee Weblink is missing data. Expected "fooResult".');

        $this->getAdapter()->request($request);
    }

    public function testRequestError(): void
    {
        $request = new Request('foo');
        $this->queueResponse('{
            "fooResult": {
                "Errors": {
                    "Error": [
                        {"Code": "foo", "Message": "bar"}
                    ]
                }
            }
        }');

        $this->expectException(WeblinkException::class);
        $this->expectExceptionMessage('foo: bar');

        $this->getAdapter()->request($request);
    }
}
