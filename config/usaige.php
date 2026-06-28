<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Path
    |--------------------------------------------------------------------------
    | The URI at which the USAIGE dashboard is accessible.
    */
    'path' => 'usaige',

    /*
    |--------------------------------------------------------------------------
    | Dashboard Middleware
    |--------------------------------------------------------------------------
    | Additional middleware applied to the dashboard route.
    | The 'web' middleware group is always included.
    |
    | Examples:
    |   ['auth']
    |   ['auth', 'can:view-usaige']
    |   ['auth:admin']
    */
    'middleware' => [],

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    | Override the default table names if they conflict with your application.
    */
    'table_names' => [
        'ai_runs' => 'ai_runs',
        'ai_usages' => 'ai_usages',
    ],
];
