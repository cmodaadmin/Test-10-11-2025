<?php
return [
    'db' => [
        'host' => 'localhost',
        'database' => 'rultivate',
        'username' => 'DB_USERNAME',
        'password' => 'DB_PASSWORD',
        'charset' => 'utf8mb4',
    ],
    'jwt' => [
        'secret' => 'CHANGE_ME_TO_A_SECURE_RANDOM_STRING',
        'issuer' => 'rultivate.in',
        'audience' => 'rultivate.clients',
        'expiry' => 3600 * 6
    ],
    'app' => [
        'base_url' => 'https://your-domain.com/api'
    ]
];
