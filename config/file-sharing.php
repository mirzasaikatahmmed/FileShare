<?php

return [
    // Upload Configuration
    'upload' => [
        'max_file_size' => env('MAX_FILE_SIZE', 100 * 1024), // 100MB in KB
        'allowed_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'txt', 'csv', 'zip', 'rar', '7z',
            'mp4', 'avi', 'mov', 'wmv', 'flv',
            'mp3', 'wav', 'ogg', 'flac'
        ],
        'blocked_extensions' => [
            'exe', 'bat', 'cmd', 'sh', 'php', 'php3', 'php4', 'php5',
            'phtml', 'js', 'html', 'htm', 'scr', 'vbs', 'jar'
        ],
    ],

    // Storage Configuration
    'storage' => [
        'disk' => env('FILESYSTEM_DISK', 'local'),
        'private_disk' => 'private',
        'public_disk' => 'public',
        'max_total_storage' => env('MAX_TOTAL_STORAGE', 100 * 1024 * 1024 * 1024), // 100GB
    ],

    // File Sharing Configuration
    'sharing' => [
        'default_expiration_days' => env('DEFAULT_EXPIRATION_DAYS', 7),
        'max_expiration_days' => env('MAX_EXPIRATION_DAYS', 30),
        'min_password_length' => env('MIN_PASSWORD_LENGTH', 6),
        'max_password_length' => env('MAX_PASSWORD_LENGTH', 50),
        'short_code_length' => env('SHORT_CODE_LENGTH', 8),
    ],

    // QR Code Configuration
    'qrcode' => [
        'size' => env('QRCODE_SIZE', 300),
        'format' => env('QRCODE_FORMAT', 'png'),
        'error_correction' => env('QRCODE_ERROR_CORRECTION', 'H'),
        'margin' => env('QRCODE_MARGIN', 2),
    ],

    // Rate Limiting
    'rate_limit' => [
        'uploads_per_ip_per_hour' => env('RATE_LIMIT_UPLOADS', 10),
        'downloads_per_ip_per_hour' => env('RATE_LIMIT_DOWNLOADS', 50),
        'access_attempts_per_file' => env('RATE_LIMIT_ACCESS_ATTEMPTS', 5),
    ],

    // Cleanup Configuration
    'cleanup' => [
        'auto_cleanup' => env('AUTO_CLEANUP_ENABLED', true),
        'cleanup_frequency' => env('CLEANUP_FREQUENCY', 'daily'),
        'delete_expired_files' => env('DELETE_EXPIRED_FILES', true),
        'retention_days' => env('FILE_RETENTION_DAYS', 30),
    ],

    // Security
    'security' => [
        'enable_malware_scan' => env('ENABLE_MALWARE_SCAN', false),
        'max_failed_attempts' => env('MAX_FAILED_ATTEMPTS', 5),
        'block_duration' => env('BLOCK_DURATION', 60), // minutes
    ],
];
