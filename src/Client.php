<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink;

use Skarabee\Weblink\ApiAdapter\ApiAdapter;
use Skarabee\Weblink\ApiAdapter\SoapApiAdapter;
use Skarabee\Weblink\Response\Response;
use Skarabee\Weblink\Response\PublicationResponse;
use Skarabee\Weblink\Response\PublicationSummaryResponse;
use Skarabee\Weblink\Response\ProjectSummaryResponse;
use Skarabee\Weblink\Response\ContactInfoResponse;
use Skarabee\Weblink\Response\LoginResponse;
use Skarabee\Weblink\Request\Request;
use DateTime;

final class Client
{
    /** @var ApiAdapter */
    private $apiAdapter;

    /** @var array */
    private $soapClientOptions;

    public function __construct(string $username, string $password, array $soap_client_options = [])
    {
        $this->soapClientOptions = array_merge([
            'login' => $username,
            'password' => $password,
        ], $soap_client_options);
    }

    // Endpoints

    /**
     * Get a list of publications.
     *
     * @param DateTime|null $modified_since Filter the list for publications
     * that have been created or modified since a specific date
     *
     * @param array|null $types Filter the list for publications of a specific
     * type of property
     *
     * @param bool|null $exclude_shared_properties Exclude publications that
     * have been shared by other agencies
     *
     * @return array
     */
    public function getPublicationSummaries(?DateTime $modified_since = null, ?array $types = null, ?bool $exclude_shared_properties = null): array
    {
        $parameters = [];

        if (!is_null($modified_since)) {
            $parameters['LastModified'] = $modified_since;
        }
        if (!is_null($types)) {
            $parameters['RequestedPropertyTypes'] = $types;
        }
        if (!is_null($exclude_shared_properties)) {
            $parameters['ExcludeSharedProperties'] = $exclude_shared_properties;
        }

        $request = new Request('GetPublicationSummaries', $parameters);
        return PublicationSummaryResponse::collection($this->getApiAdapter()->request($request));
    }

    /**
     * Get the data for a single publication.
     *
     * @param int $id The ID of the publication to request
     *
     * @return PublicationResponse
     */
    public function getPublication(int $id): PublicationResponse
    {
        $request = new Request('GetPublication', [
            'PublicationId' => $id,
        ]);
        return new PublicationResponse($this->getApiAdapter()->request($request));
    }

    /**
     * Get a list of published projects.
     *
     * @param DateTime|null $modified_since Filter the list for publications
     * that have been created or modified since a specific date
     *
     * @param bool|null $exclude_shared_properties Exclude publications that
     * have been shared by other agencies
     *
     * @return array
     */
    public function getProjectSummaries(?DateTime $modified_since = null, ?bool $exclude_shared_properties = null): array
    {
        $parameters = [];

        if (!is_null($modified_since)) {
            $parameters['LastModified'] = $modified_since;
        }
        if (!is_null($exclude_shared_properties)) {
            $parameters['ExcludeSharedProperties'] = $exclude_shared_properties;
        }

        $request = new Request('GetProjectSummaries', $parameters);
        return ProjectSummaryResponse::collection($this->getApiAdapter()->request($request));
    }

    /**
     * Get the contact info of the current agent.
     *
     * @return ContactInfoResponse
     */
    public function getContactInfo(): array
    {
        $request = new Request('GetContactInfo');
        return ContactInfoResponse::collection($this->getApiAdapter()->request($request));
    }

    /**
     * Get a list of the current agent's users.
     *
     * @return array
     */
    public function getLogins(): array
    {
        $request = new Request('GetLogins');
        return LoginResponse::collection($this->getApiAdapter()->request($request));
    }

    /**
     * Send contact form input to Skarabee.
     *
     * @param array $input Either an associative array containing the contact
     * data, or an array of associative arrays containing data for multiple
     * contact requests
     */
    public function insertContactMes(array $input): void
    {
        if (count($input) && !isset($input[0])) {
            $input = [$input];
        }
        $request = new Request('InsertContactMes', $input);
        $response = $this->getApiAdapter()->request($request);

        // Process errors
        if (!empty($response->InvalidContactMe)) {
            $contacts = [];
            foreach ($response->InvalidContactMe as $invalid_contact) {
                $contacts[] = [
                    'externalReference' => $invalid_contact->ExternalReference ?? null,
                    'errors' => $invalid_contact->Errors->string ?? [],
                ];
            }
            throw new Exception\InvalidContactMeException($contacts);
        }
    }

    /**
     * Inform Skarabee about the status of a publication.
     *
     * @param array $input Either an associative array containing feedback about
     * a single publication, or an array of associative arrays containing
     * feedback for multiple publications
     */
    public function feedback(array $input): void
    {
        // Normalize input
        if (!isset($input['FeedbackList'])) {
            $input = ['FeedbackList' => $input];
        }
        if (!isset($input['FeedbackList']['FeedbackList'])) {
            $input['FeedbackList'] = ['FeedbackList' => $input['FeedbackList']];
        }
        if (is_array($input['FeedbackList']['FeedbackList']) && count($input['FeedbackList']['FeedbackList']) && !isset($input['FeedbackList']['FeedbackList'][0])) {
            $input['FeedbackList']['FeedbackList'] = [$input['FeedbackList']['FeedbackList']];
        }

        $request = new Request('Feedback', $input);
        $response = $this->getApiAdapter()->request($request);
    }

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
        $this->getApiAdapter()->debugResponses($callable);
        return $this;
    }

    // Api adapter

    public function getSoapClientOptions(): array
    {
        return $this->soapClientOptions;
    }

    public function setApiAdapter(ApiAdapter $adapter): self
    {
        $this->apiAdapter = $adapter;
        return $this;
    }

    public function getApiAdapter(): ApiAdapter
    {
        if (!isset($this->apiAdapter)) {
            $this->setApiAdapter(new SoapApiAdapter($this->getSoapClientOptions()));
        }
        return $this->apiAdapter;
    }
}
