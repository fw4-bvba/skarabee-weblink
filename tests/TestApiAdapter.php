<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Tests;

use Skarabee\Weblink\ApiAdapter\ApiAdapter;
use Skarabee\Weblink\Request\Request;

final class TestApiAdapter extends ApiAdapter
{
    /** @var array */
    protected $responseQueue = [];

    public function clearQueue(): void
    {
        $this->responseQueue = [];
    }

    public function queueResponse(string $body): void
    {
        $this->responseQueue[] = json_decode($body, false);
    }

    /**
     * {@inheritdoc}
     */
    public function executeRequest(Request $request): ?object
    {
        return array_shift($this->responseQueue);
    }
}
