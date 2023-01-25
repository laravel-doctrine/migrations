<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Entity Manager Migrations Configuration
    |--------------------------------------------------------------------------
    |
    | Each entity manager can have a custom migration configuration. Provide
    | the name of the entity manager as the key, then duplicate the settings.
    | This will allow generating custom migrations per EM instance and not have
    | collisions when executing them.
    |
    */
    'default' => [
        'table_storage' => [
            /*
            |--------------------------------------------------------------------------
            | Migration Repository Table
            |--------------------------------------------------------------------------
            |
            | This table keeps track of all the migrations that have already run for
            | your application. Using this information, we can determine which of
            | the migrations on disk haven't actually been run in the database.
            |
            */
            'table_name'     => 'migrations',

            /*
            |--------------------------------------------------------------------------
            | Schema filter
            |--------------------------------------------------------------------------
            |
            | Tables which are filtered by Regular Expression. You optionally
            | exclude or limit to certain tables. The default will
            | filter all tables.
            |
            */
            'schema_filter'    => '/^(?!password_resets|failed_jobs).*$/'
        ],

        'migrations_paths' => [
            'Database\\Migrations' => database_path('migrations')
        ],

        /*
        |--------------------------------------------------------------------------
        | Migration Organize Directory
        |--------------------------------------------------------------------------
        |
        | Organize migrations file by directory.
        | Possible values: "year", "year_and_month" and "none"
        |
        | none:
        |    directory/
        | "year":
        |    directory/2020/
        | "year_and_month":
        |    directory/2020/01/
        |
        */
        'organize_migrations' => 'none',
    ],
];
