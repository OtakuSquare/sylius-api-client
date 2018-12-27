<?php

namespace OtakuSquare\SyliusApiClient\Helper;

/**
 * Class ProductHelper
 * @package OtakuSquare\SyliusApiClient\Helper
 */
class ProductHelper extends BaseHelper
{
    /**
     * @param int|null $limit
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listProducts($limit = null)
    {
        $endpoint = '/v1/products';

        if ($limit !== null) {
            $endpoint .= '?limit=' . $limit;
        }

        $response = $this->syliusClient->abstractMultipartRequest('GET', $endpoint);

        return json_decode($response->getBody(), true);
    }
}