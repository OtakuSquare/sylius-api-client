<?php

namespace OtakuSquare\SyliusApiClient\Helper;

use OtakuSquare\SyliusApiClient\SyliusClient;

/**
 * Class BaseHelper
 * @package OtakuSquare\SyliusApiClient\Helper
 */
class BaseHelper
{
    /**
     * @var SyliusClient
     */
    protected $syliusClient;

    /**
     * @param SyliusClient $syliusClient
     */
    public function setSyliusClient(SyliusClient $syliusClient)
    {
        $this->syliusClient = $syliusClient;
    }
}