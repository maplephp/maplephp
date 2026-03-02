<?php

/**
 * Default configs, that exists in MaplePHP Unitary
 */

return [
    'title' => getenv('APP_TITLE'),
    'env' => getenv('APP_ENV'),
    'type' => "cli",
    'verbose' => false,
    'timezone' => 'UTC',
    'locale' => 'en_US',
    'helpController' => "\App\Controllers\HelpController"
];
