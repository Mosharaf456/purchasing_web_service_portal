<?php

return [
    'key' => env('JWT_SECRET_KEY'),
    'alg' => env('JWT_ALG', 'HS512'),
    'access_ttl' => (int) env('JWT_ACCESS_TOKEN_EXPIRY', 900),
    'refresh_ttl' => (int) env('JWT_REFRESH_TOKEN_EXPIRY', 86400),
];
