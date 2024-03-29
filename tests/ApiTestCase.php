<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Tests;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Skarabee\Weblink\Client;

abstract class ApiTestCase extends TestCase
{
    /** @var TestApiAdapter */
    protected static $adapter;

    /** @var Client */
    protected static $client;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$adapter = new TestApiAdapter();
        self::$client = new Client('', '');
        self::$client->setApiAdapter(self::$adapter);
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::$adapter->clearQueue();
        self::$adapter->debugResponses(null);
    }

    /**
     * @param string|JsonSerializable $body
     */
    public function queueResponse($body): void
    {
        if (!is_string($body)) {
            $body = json_encode($body) ?: '{}';
        }
        self::$adapter->queueResponse($body);
    }

    /**
     * @return array<mixed>|null
     */
    public function getLastRequestParameters(): ?array
    {
        $request = self::$adapter->getLastRequest();
        if (empty($request)) {
            return null;
        }
        return $request->getParameters();
    }

    public function getClient(): Client
    {
        return self::$client;
    }

    public function getAdapter(): TestApiAdapter
    {
        return self::$adapter;
    }
}
