<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Tests;

use Skarabee\Weblink\Client;
use Skarabee\Weblink\ApiAdapter\SoapApiAdapter;
use Skarabee\Weblink\Enums\PropertyType;
use Skarabee\Weblink\Exception\InvalidContactMeException;
use DateTime;

class ClientTest extends ApiTestCase
{
    public function testGetPublicationSummaries(): void
    {
        $this->queueResponse('{
            "GetPublicationSummariesResult": {
                "PublicationSummaries": {
                    "PublicationSummary": [
                        {"ID": 1},
                        {"ID": 2},
                        {"ID": 3}
                    ]
                }
            }
        }');

        $result = $this->getClient()->getPublicationSummaries(new DateTime(), [
            PropertyType::Lot,
            PropertyType::Model,
        ], true);

        $this->assertCount(3, $result);
        $this->assertEquals(2, $result[1]->id);

        $request = $this->getLastRequestParameters();
        $this->assertIsString($request['LastModified']);
        $this->assertEquals(['LOT', 'MODEL'], $request['RequestedPropertyTypes']);
        $this->assertEquals(true, $request['ExcludeSharedProperties']);
    }

    public function testGetPublication(): void
    {
        $this->queueResponse('{
            "GetPublicationResult": {
                "Publication": {
                    "Info": {"ID": 1},
                    "Property": {"ID": 2}
                }
            }
        }');

        $result = $this->getClient()->getPublication(1);

        $this->assertEquals(1, $result->info->id);
        $this->assertEquals(2, $result->property->id);

        $request = $this->getLastRequestParameters();
        $this->assertEquals(1, $request['PublicationId']);
    }

    public function testGetPublicationNullResult(): void
    {
        $this->queueResponse('{
            "GetPublicationResult": {}
        }');

        $result = $this->getClient()->getPublication(1);

        $this->assertNull($result);
    }

    public function testGetProjectSummaries(): void
    {
        $this->queueResponse('{
            "GetProjectSummariesResult": {
                "ProjectPublicationSummaries": {
                    "ProjectPublicationSummary": [
                        {"ID": 1},
                        {"ID": 2},
                        {"ID": 3}
                    ]
                }
            }
        }');

        $result = $this->getClient()->getProjectSummaries(new DateTime(), true);

        $this->assertCount(3, $result);
        $this->assertEquals(2, $result[1]->id);

        $request = $this->getLastRequestParameters();
        $this->assertIsString($request['LastModified']);
        $this->assertEquals(true, $request['ExcludeSharedProperties']);
    }

    public function testGetContactInfo(): void
    {
        $this->queueResponse('{
            "GetContactInfoResult": {
                "UserSummaries": {
                    "UserSummary": [
                        {"ID": 1},
                        {"ID": 2},
                        {"ID": 3}
                    ]
                }
            }
        }');

        $result = $this->getClient()->getContactInfo();

        $this->assertCount(3, $result);
        $this->assertEquals(2, $result[1]->id);
    }

    public function testGetLogins(): void
    {
        $this->queueResponse('{
            "GetLoginsResult": {
                "LoginInfo": {
                    "Login": [
                        {"ID": 1},
                        {"ID": 2},
                        {"ID": 3}
                    ]
                }
            }
        }');

        $result = $this->getClient()->getLogins();

        $this->assertCount(3, $result);
        $this->assertEquals(2, $result[1]->id);
    }

    public function testInsertContactMes(): void
    {
        $this->queueResponse('{
            "InsertContactMesResult": {
                "InvalidContactMes": {
                    "InvalidContactMe": []
                }
            }
        }');

        $result = $this->getClient()->insertContactMes([
            'ExternalReference' => 'foo'
        ]);

        $this->assertNull($result);

        $request = $this->getLastRequestParameters();
        $this->assertEquals('foo', $request[0]['ExternalReference']);
    }

    public function testInsertContactMesMultiple(): void
    {
        $this->queueResponse('{
            "InsertContactMesResult": {
                "InvalidContactMes": {
                    "InvalidContactMe": []
                }
            }
        }');

        $result = $this->getClient()->insertContactMes([
            ['ExternalReference' => 'foo'],
            ['ExternalReference' => 'bar'],
        ]);

        $this->assertNull($result);

        $request = $this->getLastRequestParameters();
        $this->assertEquals('foo', $request[0]['ExternalReference']);
        $this->assertEquals('bar', $request[1]['ExternalReference']);
    }

    public function testInsertContactMesInvalid(): void
    {
        $this->queueResponse('{
            "InsertContactMesResult": {
                "InvalidContactMes": {
                    "InvalidContactMe": [
                        {
                            "ExternalReference": "foo",
                            "Errors": {
                                "string": ["bar", "baz"]
                            }
                        }
                    ]
                }
            }
        }');

        $this->expectException(InvalidContactMeException::class);
        $this->expectExceptionMessage('InsertContactMes resulted in 2 issues with 1 contact: bar. baz.');

        $result = $this->getClient()->insertContactMes([
            'ExternalReference' => 'foo'
        ]);
    }

    public function testFeedback(): void
    {
        $this->queueResponse('{
            "FeedbackResult": {}
        }');

        $result = $this->getClient()->feedback([
            'ExternalID' => 'foo'
        ]);

        $this->assertNull($result);

        $request = $this->getLastRequestParameters();
        $this->assertEquals('foo', $request['FeedbackList']['FeedbackList'][0]['ExternalID']);
    }

    public function testGetSoapClientOptions(): void
    {
        $client = new Client('foo', 'bar', [
            'baz' => 'qux',
        ]);

        $options = $client->getSoapClientOptions();
        $this->assertEquals('foo', $options['login']);
        $this->assertEquals('bar', $options['password']);
        $this->assertEquals('qux', $options['baz']);
    }

    public function testApiAdapter(): void
    {
        $client = new Client('foo', 'bar');

        $this->assertInstanceOf(SoapApiAdapter::class, $client->getApiAdapter());

        $adapter = new TestApiAdapter();

        $client->setApiAdapter($adapter);

        $this->assertEquals($adapter, $client->getApiAdapter());
    }

    public function testDebugResponses(): void
    {
        $client = new Client('foo', 'bar');

        $called = false;
        $this->getClient()->debugResponses(function($function, $request, $response) use (&$called) {
            $called = true;

            $this->assertEquals('GetPublication', $function);
            $this->assertEquals(['PublicationId' => 1], $request);
            $this->assertEquals(1, $response->GetPublicationResult->Publication->Info->ID);
        });

        $this->queueResponse('{
            "GetPublicationResult": {
                "Publication": {
                    "Info": {"ID": 1},
                    "Property": {"ID": 2}
                }
            }
        }');

        $result = $this->getClient()->getPublication(1);

        $this->assertTrue($called);
    }
}
