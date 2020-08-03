<?php

return [
    'appUrl' => [
        'id' => 1,
        'module_id' => 2,
        'entity_id' => 1,
        'url' => '/some-url',
        'redirect_to_url' => null,
    ],
    'shopUrl1' => [
        'id' => 2,
        'module_id' => 3,
        'entity_id' => 1,
        'url' => '/product-one',
        'redirect_to_url' => null,
    ],
    'shopUrl2' => [
        'id' => 3,
        'module_id' => 3,
        'entity_id' => 2,
        'url' => '/product-two',
        'redirect_to_url' => null,
    ],
    'shopRedirect1' => [
        'id' => 4,
        'module_id' => 1,
        'entity_id' => 302,
        'url' => '/product-one-first-redirect',
        'redirect_to_url' => 2,
    ],
    'shopRedirect2' => [
        'id' => 5,
        'module_id' => 1,
        'entity_id' => 302,
        'url' => '/product-one-second-redirect',
        'redirect_to_url' => 2,
    ],
];
