<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Tests\Enums;

use Skarabee\Weblink\Enums\PropertyType;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function testAll(): void
    {
        $this->assertEquals([
            'Transaction' => 'TRANSACTION',
            'Project' => 'PROJECT',
            'Lot' => 'LOT',
            'Model' => 'MODEL',
        ], PropertyType::all());
    }
}
