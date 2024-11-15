<?php

return [
    'displayErrorDetails' => true,
    'logErrors' => true,
    'logErrorDetails' => true,
    'jwt' => [
        'secret' => getenv('JWT_SECRET'),
        'expiry' => 3600 // 1 hour
    ]
];
