<?php

return [
    // Paths to apply CORS to
    'paths' => ['api/*'],

    // Allowed methods
    'allowed_methods' => ['*'],

    // Allowed origins (no wildcard when using credentials)
    'allowed_origins' => ['*'],

    // Allowed origin patterns (none)
    'allowed_origins_patterns' => [],

    // Allowed headers
    'allowed_headers' => ['*'],

    // Exposed headers
    'exposed_headers' => [],

    // Max age
    'max_age' => 0,

    'supports_credentials' => false,
];
