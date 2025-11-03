<?php

return [
    'default' => 'main',

    'connections' => [
        'main' => [
            'salt' => env('HASHIDS_SALT', 'file-share-secret-salt'),
            'length' => env('HASHIDS_LENGTH', 8),
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],
    ],
];
