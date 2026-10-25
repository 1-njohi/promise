<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],   // routes to apply CORS
    'allowed_methods' => ['*'],                    // or ['GET', 'POST', ...]
    'allowed_origins' => ['http://localhost:8000', 'https://0a19-102-0-16-184.ngrok-free.app', 'http://localhost:8001'], // never use '*' in production
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,               // set true if using cookies/session auth
];