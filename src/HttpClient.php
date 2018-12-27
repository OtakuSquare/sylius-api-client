<?php

namespace OtakuSquare\SyliusApiClient;

use OtakuSquare\SyliusApiClient\Authorization\ApiCredentials;
use OtakuSquare\SyliusApiClient\Exception\InvalidBaseUriException;
use OtakuSquare\SyliusApiClient\Exception\NotAuthenticatedException;

/**
 * Class HttpClient
 * @package OtakuSquare\SyliusApiClient
 */
class HttpClient
{
    /**
     * @var string|null
     */
    public $bearerToken;

    /**
     * @var int|null
     */
    private $bearerTokenExpires;

    /**
     * @var string|null
     */
    private $refreshToken;

    /**
     * @var int|null
     */
    private $refreshTokenExpires;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var ApiCredentials
     */
    private $apiCredentials;

    /**
     * HttpClient constructor.
     * @param string $baseUri
     * @param ApiCredentials $apiCredentials
     * @throws InvalidBaseUriException
     */
    public function __construct($baseUri, ApiCredentials $apiCredentials)
    {
        $this->bearerToken = null;
        $this->bearerTokenExpires = null;
        $this->refreshToken = null;
        $this->refreshTokenExpires = null;

        $this->baseUri = $baseUri;
        $this->apiCredentials = $apiCredentials;

        $this->requestAuthenticationTokens();
    }

    /**
     * @param string $endPoint
     * @return resource
     */
    public function generateGenericCurl($endPoint)
    {
        $curl = curl_init($this->baseUri . $endPoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Sylius Api Client by Otaku Square');

        return $curl;
    }

    public function getSecuredCurl($endPoint)
    {
        if ($this->bearerToken === null) {
            throw new NotAuthenticatedException('You are not authenticated, authenticate first before doing this.');
        }

        $curl = $this->generateGenericCurl($endPoint);
        $this->setCurlHeaders($curl, ['Authorization: Bearer ' . $this->bearerToken]);

        return $curl;
    }

    /**
     * @param resource $curl
     * @return string
     */
    public function executeCurlRequest($curl)
    {
        return curl_exec($curl);
    }

    /**
     * @param resource $curl
     * @param array $formData
     */
    public function setCurlFormData($curl, $formData)
    {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $formData);
    }

    /**
     * @param resource $curl
     * @param array $headers
     */
    public function setCurlHeaders($curl, $headers)
    {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * @param resource $curl
     * @return array
     */
    public function getCurlArrayResponse($curl)
    {
        $response = curl_exec($curl);
        return json_decode($response, true);
    }

    public function requestAuthenticationTokens()
    {
        $curl = $this->generateGenericCurl('/oauth/v2/token');

        $this->setCurlFormData($curl, [
            'client_id' => $this->apiCredentials->clientId,
            'client_secret' => $this->apiCredentials->clientSecret,
            'grant_type' => $this->apiCredentials->grantType,
            'username' => $this->apiCredentials->username,
            'password' => $this->apiCredentials->password
        ]);

        $response = $this->getCurlArrayResponse($curl);

        if (array_key_exists('access_token', $response)) {
            $this->bearerToken = $response['access_token'];
            $this->bearerTokenExpires = time() + $response['expires_in'];
            $this->refreshToken = $response['refresh_token'];
        }
    }
}