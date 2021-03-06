<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\ApiAdapter;

use Skarabee\Weblink\Request\Request;
use Skarabee\Weblink\Exception;
use PackageVersions\Versions;

/**
 * @codeCoverageIgnore
 */
final class SoapApiAdapter extends ApiAdapter
{
    private const DEFAULT_TIMEOUT = 30;

    /** @var SoapClient */
    private $client;

    public function __construct(array $soap_client_options = [])
    {
        if (empty($soap_client_options['user_agent'])) {
            $version = Versions::getVersion('fw4/skarabee-weblink');
            $soap_client_options['user_agent'] = 'fw4-skarabee-weblink/' . $version;
        }

        $this->client = new \SoapClient(__DIR__ . DIRECTORY_SEPARATOR . 'weblink.asmx.xml', array_merge([
            'soap_version' => SOAP_1_2,
            'exceptions' => false,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'connection_timeout' => self::DEFAULT_TIMEOUT,
            'trace' => true,
            'typemap' => [
                [
                    'type_ns' => 'http://www.w3.org/2001/XMLSchema',
                    'type_name' => 'decimal',
                    'from_xml' => [$this, 'parseDecimal'],
                ],
                [
                    'type_ns' => 'http://weblink.skarabee.com/',
                    'type_name' => 'AddressCoordinate',
                    'from_xml' => [$this, 'parseCoordinate'],
                ],
            ],
        ], $soap_client_options));
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception\WeblinkException if the weblink encounters an error
     * @throws Exception\AuthException if access is denied
     */
    public function executeRequest(Request $request): ?object
    {
        $function = $request->getFunction();
        $parameters = $request->getParameters();

        $response = $this->client->__soapCall($function, [$parameters]);

        if ($response instanceof \SoapFault) {
            if ($response->getMessage() === 'Unauthorized') {
                throw new Exception\AuthException($response->getMessage());
            }
            throw new Exception\WeblinkException($response->getMessage());
        }

        return $response;
    }

    /**
     * Parse a decimal value, which gets formatted as a string by SOAP.
     *
     * @param string $value
     *
     * @return null|float
     */
    public function parseDecimal(string $value): ?float
    {
        $xml = simplexml_load_string($value);
        $string = strval($xml);
        if ($string === '-1' || $string === '') {
            return null;
        } else {
            return floatval(str_replace(',', '.', $string));
        }
    }

    /**
     * Parse geometric coordinates.
     *
     * @param string $value
     *
     * @return object
     */
    public function parseCoordinate(string $value): object
    {
        $xml = simplexml_load_string($value);
        $value = (object)((array)$xml->attributes())['@attributes'];
        $value->X = empty($value->X) ? null : floatval(str_replace(',', '.', $value->X));
        $value->Y = empty($value->Y) ? null : floatval(str_replace(',', '.', $value->Y));
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdditionalDebugCallbackArguments(): array
    {
        return [$this->client->__getLastRequest(), $this->client->__getLastResponse()];
    }
}
