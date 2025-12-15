<?php

return [
    // Paths to apply CORS to
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Allowed methods
    'allowed_methods' => ['*'],

    // Allowed origins (no wildcard when using credentials)
    'allowed_origins' => ['http://localhost:3001', 'http://127.0.0.1:3001'],

    // Allowed origin patterns (none)
    'allowed_origins_patterns' => [],

    // Allowed headers
    'allowed_headers' => ['*'],

    // Exposed headers
    'exposed_headers' => [],

    // Max age
    'max_age' => 0,

    // Supports credentials (cookies)
    'supports_credentials' => true,
];
