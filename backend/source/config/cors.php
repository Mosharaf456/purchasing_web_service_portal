<?php
return [
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => [env('CLIENT_URL', 'http://127.0.0.1:8000')] ,
'allowed_headers' => ['Authorization', 'Content-Type'],
'exposed_headers' => ['Authorization'],
'max_age' => 0,
'supports_credentials' => false,
];