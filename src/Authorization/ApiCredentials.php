<?php

namespace OtakuSquare\SyliusApiClient\Authorization;

/**
 * Class ApiCredentials
 * @package OtakuSquare\SyliusApiClient\Authorization
 */
class ApiCredentials
{
    /**
     * @var string
     */
    public $clientId;

    /**
     * @var string
     */
    public $clientSecret;

    /**
     * @var string|null
     */
    public $grantType;

    /**
     * @var string|null
     */
    public $username;

    /**
     * @var string|null
     */
    public $password;

    // FOR INTERNAL USE ONLY

    /**
     * @var string|null
     */
    public $accessToken;

    /**
     * @var int|null
     */
    public $accessTokenExpires;

    /**
     * @var string|null
     */
    public $refreshToken;

    /**
     * ApiCredentials constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param string|null $username
     * @param string|null $password
     */
    public function __construct($clientId, $clientSecret, $username = null, $password = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        if ($username !== null) {
            $this->grantType = 'password';

            $this->username = $username;
            $this->password = $password;
        }
    }
}