<?php

return [
    'admin_email' => env('ADMIN_EMAIL', 'admin@digitaldimension.test'),
    'admin_password' => env('ADMIN_PASSWORD', 'password'),

    'uploads' => [
        'max_image_kb' => (int) env('UPLOAD_MAX_IMAGE_KB', 4096),
        'max_document_kb' => (int) env('UPLOAD_MAX_DOCUMENT_KB', 10240),
        'image_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
        'document_mimes' => ['application/pdf'],
    ],

    'pagination' => [
        'projects' => 9,
        'journal' => 9,
        'certificates' => 12,
        'guestbook' => 12,
    ],
];
