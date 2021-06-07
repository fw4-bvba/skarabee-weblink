<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Enums;

final class PropertyType extends Enum
{
    const Transaction = 'TRANSACTION';
    const Project     = 'PROJECT';
    const Lot         = 'LOT';
    const Model       = 'MODEL';
}
