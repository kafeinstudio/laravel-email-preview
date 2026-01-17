<?php

return [
    // The storage path of the emails.
    'path'       => storage_path('email-previews'),

    // The lifetime of the emails, In secondes.
    'lifeTime'   => 60 * 60 * 24,

    // If not empty, only those IPs (comma separated) will access to the emails.
    'allowedIps' => array_filter(explode(',', env('EMAIL_PREVIEW_IPS', ''))),
];
