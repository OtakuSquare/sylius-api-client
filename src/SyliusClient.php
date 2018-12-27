<?php

namespace OtakuSquare\SyliusApiClient;

use GuzzleHttp\Client;
use OtakuSquare\SyliusApiClient\Authorization\ApiCredentials;
use OtakuSquare\SyliusApiClient\Exception\InvalidApiResponseException;

/**
 * Class SyliusClient
 * @package OtakuSquare\SyliusApiClient
 */
class SyliusClient
{
    /**
     * @var Client
     */
    private $guzzleClient;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var boolean
     */
    private $autoRefresh;

    /**
     * @var ApiCredentials
     */
    private $apiCredentials;

    /**
     * SyliusClient constructor.
     * @param string $baseUri
     */
    public function __construct($baseUri)
    {
        $this->baseUri = $baseUri;

        $this->guzzleClient = new Client();
    }

    /**
     * @param ApiCredentials $apiCredentials
     * @param bool $autoRefresh
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidApiResponseException
     */
    public function authenticate(ApiCredentials $apiCredentials, $autoRefresh = true)
    {
        $this->autoRefresh = $autoRefresh;
        $this->apiCredentials = $apiCredentials;

        $authenticationResponse = $this->abstractRequest('POST', '/oauth/v2/token', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'data' => json_encode($this->buildAuthenticationArray())
        ]);

        $decodedResponse = json_decode($authenticationResponse);

        if (!is_object($decodedResponse) || !property_exists($decodedResponse, 'access_token')) {
            throw new InvalidApiResponseException();
        }

        $this->apiCredentials->accessToken = $decodedResponse->access_token;
        $this->apiCredentials->accessTokenExpires = $decodedResponse->expires_in + time();
        $this->apiCredentials->refreshToken = $decodedResponse->refresh_token;

        return true;
    }

    /**
     * @return bool
     * @throws InvalidApiResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshAuthentication()
    {
        $this->apiCredentials->grantType = 'refresh_token';

        return $this->authenticate($this->apiCredentials);
    }

    /**
     * @param $method
     * @param $endpoint
     * @param $data
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * Note: do NOT use for authentication
     */
    public function abstractMultipartRequest($method, $endpoint, $data = [])
    {
        return $this->abstractRequest($method, $endpoint, [
            'multipart' => $this->buildMultipartArray($data),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiCredentials->accessToken,
            ]
        ]);
    }

    /**
     * @param $method
     * @param $endpoint
     * @param $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function abstractRequest($method, $endpoint, $options)
    {
        return $this->guzzleClient->request($method, $this->baseUri . $endpoint, $options);
    }

    /**
     * @param array $regularArray
     * @return array
     */
    private function buildMultipartArray($regularArray)
    {
        $output = [];

        foreach($regularArray as $key => $value) {
            if(!is_array($value) ){
                $output[] = ['name' => $key, 'contents' => $value];
                continue;
            }

            foreach($value as $multiKey => $multiValue) {
                $multiName = $key . '[' .$multiKey . ']' . (is_array($multiValue) ? '[' . key($multiValue) . ']' : '' ) . '';
                $output[] = ['name' => $multiName, 'contents' => (is_array($multiValue) ? reset($multiValue) : $multiValue)];
            }
        }

        return $output;
    }

    /**
     * @return array
     */
    private function buildAuthenticationArray()
    {
        if ($this->apiCredentials->grantType === 'password') {
            return [
                'client_id' => $this->apiCredentials->clientId,
                'client_secret' => $this->apiCredentials->clientSecret,
                'grant_type' => $this->apiCredentials->grantType,
                'username' => $this->apiCredentials->username,
                'password' => $this->apiCredentials->password
            ];
        }

        return [
            'client_id' => $this->apiCredentials->clientId,
            'client_secret' => $this->apiCredentials->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->apiCredentials->refreshToken
        ];
    }
}