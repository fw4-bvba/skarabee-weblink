<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Enums;

final class FeedbackStatus extends Enum
{
    const Available      = 'AVAILABLE';
    const Deleted        = 'DELETED';
    const AgentNotActive = 'AGENT_NOT_ACTIVE';
    const Error          = 'ERROR';
}
