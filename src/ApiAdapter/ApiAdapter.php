<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\ApiAdapter;

use Skarabee\Weblink\Request\Request;
use Skarabee\Weblink\Response\ResponseObject;
use Skarabee\Weblink\Response\ResponseData;
use Skarabee\Weblink\Exception;

abstract class ApiAdapter
{
    /** @var callable */
    protected $debugCallable;

    /** @var Request */
    protected $lastRequest;

    /**
     * Send a request to the API and process the response.
     *
     * @param Request $request
     *
     * @throws Exception\WeblinkException if the weblink encounters an error
     * @throws Exception\AuthException if access is denied
     *
     * @return object
     */
    public function request(Request $request): ?object
    {
        $this->lastRequest = $request;

        $response = $this->executeRequest($request);

        // Send response to debug callback
        if (isset($this->debugCallable)) {
            call_user_func_array($this->debugCallable, array_merge([
                $request->getFunction(),
                $request->getParameters(),
                $response,
            ], $this->getAdditionalDebugCallbackArguments()));
        }

        // Check for response data
        $result_container = $request->getFunction() . 'Result';
        if (!property_exists($response, $result_container)) {
            throw new Exception\WeblinkException('Response from Skarabee Weblink is missing data. Expected "' . $result_container . '".');
        }
        $response = $response->$result_container;

        // Process errors
        if (!empty($response->Errors->Error)) {
            foreach ($response->Errors->Error as $error) {
                throw new Exception\WeblinkException($error->Code . ': ' . $error->Message);
            }
        }

        unset($response->Errors);

        $response = (array)$response;
        $response = reset($response);
        return is_object($response) ? $response : null;
    }

    /**
     * Send a request to the API and return the raw response.
     *
     * @param Request $request
     *
     * @return object
     */
    abstract public function executeRequest(Request $request): ?object;

    // Debugging

    /**
     * Set a callback for debugging SOAP requests and responses.
     *
     * @param callable|null $callable Callback that accepts up to five
     * arguments - respectively the SOAP function name, the request parameters,
     * the parsed response, the XML request body, and the XML response body.
     *
     * @return self
     */
    public function debugResponses(?callable $callable): self
    {
        $this->debugCallable = $callable;
        return $this;
    }

    /**
     * Implement getAdditionalDebugCallbackArguments in a child class to add
     * extra arguments to the debug callback call.
     *
     * @return array
     */
    protected function getAdditionalDebugCallbackArguments(): array
    {
        return [];
    }

    /**
     * Get the previously executed request.
     *
     * @return null|Request
     */
    public function getLastRequest(): ?Request
    {
        return $this->lastRequest;
    }
}
