<?php

return [
    'host' => getenv('DB_HOST') ?: 'db',
    'dbname' => getenv('DB_NAME') ?: 'blog',
    'username' => getenv('DB_USER') ?: 'bloguser',
    'password' => getenv('DB_PASSWORD') ?: 'blogpass',
];
