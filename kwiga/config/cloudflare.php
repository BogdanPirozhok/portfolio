<?php

return [
    'account_email' => env('CLOUDFLARE_ACCOUNT_EMAIL'),
    'auth_key' => env('CLOUDFLARE_AUTH_KEY'),
    'api_endpoint' => env('CLOUDFLARE_API_ENDPOINT'),
    'cname_record' => env('CLOUDFLARE_CNAME_AWS_BALANCER'),
    'ssl_host_zone_id' => env('CLOUDFLARE_SSL_HOST_ZONE_ID'),
    'ssl_host_cname' => env('CLOUDFLARE_SSL_HOST_CNAME'),
    'available_stages' => ['ojowo.co'],
];
