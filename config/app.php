<?php

return [
    'name' => getenv('APP_NAME') ?: 'ERP System',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN),
    'url' => getenv('APP_URL') ?: 'http://localhost',
    'key' => getenv('APP_KEY') ?: 'change-this-in-production',

    'session' => [
        'lifetime' => (int)(getenv('SESSION_LIFETIME') ?: 7200),
        'name' => getenv('SESSION_NAME') ?: 'erp_session',
    ],

    'timezone' => 'America/New_York',
    'locale' => 'en',
    'date_format' => 'Y-m-d',
    'datetime_format' => 'Y-m-d H:i:s',
];
