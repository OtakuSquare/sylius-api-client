# Sylius Api Client
By Otaku Square  
**Under heavy development**

#### What is this?
This project seeks to be an easy to use API client for the latest Sylius REST API, with it's continued 
development encouraged by it's use in an up-to-date Sylius environment.  
  
#### For who is this?
This client is useful for anybody utilizing the Sylius API through PHP.

#### Installation
Simply use the following command: `composer require otakusquare/sylius-api-client`

#### Sample
**This is only for development purposes, and will drastically change!**
```php
<?php

require_once __DIR__ . 'vendor/autoload.php';

$client = new \OtakuSquare\SyliusApiClient\SyliusClient(
    'https://www.domain.com/api'
);

$client->authenticate(
    new \OtakuSquare\SyliusApiClient\Authorization\ApiCredentials(
        '<clientId>',
        '<clientSecret>',
        '<username>',
        '<password>'
    )
);

$productsResponse = $client->abstractMultipartRequest('GET', '/v1/products/?limit=999');
var_dump(json_decode($productsResponse->getBody(), true));
```

#### Licensing terms
This product is free to use under the MIT license, a.k.a. do whatever you desire with this.