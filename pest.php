<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Test Directories
    |--------------------------------------------------------------------------
    |
    | Here you may specify test directories that will be used by Pest.
    | You can add as many test directories as needed.
    |
    */
    'test_directories' => [
        'tests',
    ],

    /*
    |--------------------------------------------------------------------------
    | Parallel Testing
    |--------------------------------------------------------------------------
    |
    | Here you may configure parallel testing for Pest.
    |
    */
    'parallel' => false,

    /*
    |--------------------------------------------------------------------------
    | Coverage
    |--------------------------------------------------------------------------
    |
    | Here you may configure coverage settings for Pest.
    |
    */
    'coverage' => [
        'min' => 70,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins
    |--------------------------------------------------------------------------
    |
    | Here you may specify the Pest plugins you want to use.
    |
    */
    'plugins' => [
        'pestphp/pest-plugin-laravel',
    ],
];
