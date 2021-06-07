<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Response;

class ProjectSummaryResponse extends Response
{
    public function __construct($data)
    {
        parent::__construct($data, [
            'PropertySummaries',
        ]);
    }
}
