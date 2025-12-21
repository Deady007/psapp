<?php

return [
    'root_folder_id' => env('GOOGLE_DRIVE_ROOT_FOLDER_ID'),
    'trash_folder_id' => env('GOOGLE_DRIVE_TRASH_FOLDER_ID'),
    'credentials_path' => env('GOOGLE_DRIVE_CREDENTIALS_PATH'),
    'service_account' => [
        'client_email' => env('GOOGLE_DRIVE_CLIENT_EMAIL'),
        'private_key' => env('GOOGLE_DRIVE_PRIVATE_KEY')
            ? str_replace('\\n', "\n", env('GOOGLE_DRIVE_PRIVATE_KEY'))
            : null,
    ],
    'impersonate_user' => env('GOOGLE_DRIVE_IMPERSONATE_USER'),
    'token_uri' => env('GOOGLE_DRIVE_TOKEN_URI', 'https://oauth2.googleapis.com/token'),
    'drive_api_url' => env('GOOGLE_DRIVE_API_URL', 'https://www.googleapis.com/drive/v3'),
    'upload_api_url' => env('GOOGLE_DRIVE_UPLOAD_API_URL', 'https://www.googleapis.com/upload/drive/v3'),
    'retry_times' => env('GOOGLE_DRIVE_RETRY_TIMES', 3),
    'retry_sleep_ms' => env('GOOGLE_DRIVE_RETRY_SLEEP_MS', 250),
    'timeout_seconds' => env('GOOGLE_DRIVE_TIMEOUT_SECONDS', 30),
    'hard_delete_delay_minutes' => env('GOOGLE_DRIVE_HARD_DELETE_DELAY_MINUTES'),
    'verify_ssl' => env('GOOGLE_DRIVE_SSL_VERIFY', true),
    'supports_all_drives' => env('GOOGLE_DRIVE_SUPPORTS_ALL_DRIVES', true),
];
