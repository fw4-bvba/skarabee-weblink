<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Tests\Exception;

use Skarabee\Weblink\Exception\InvalidContactMeException;
use PHPUnit\Framework\TestCase;

class InvalidContactMeExceptionTest extends TestCase
{
    public function testGetContacts(): void
    {
        $exception = new InvalidContactMeException([
            ['errors' => ['foo', 'bar']],
            ['errors' => ['baz', 'qux']],
        ]);

        $this->assertCount(2, $exception->getContacts());
    }
}
