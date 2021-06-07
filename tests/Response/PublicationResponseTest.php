<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Tests\Response;

use Skarabee\Weblink\Response\PublicationResponse;
use PHPUnit\Framework\TestCase;

class PublicationResponseTest extends TestCase
{
    public function testSurfaceStock(): void
    {
        $response = new PublicationResponse(json_decode('{
            "Property": {
                "SurfaceStock": "1,234"
            }
        }', false));

        $this->assertIsFloat($response->property->surfaceStock);
        $this->assertEquals(1.234, $response->property->surfaceStock);
    }
}
