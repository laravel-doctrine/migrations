<?php

return [
    'name'            => 'Doctrine Migrations',
    'namespace'       => 'Database\\Migrations',
    'table'           => 'migrations',
    'directory'       => database_path('migrations'),
    'naming_strategy' => LaravelDoctrine\Migrations\Naming\LaravelNamingStrategy::class
];
