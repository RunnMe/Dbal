<?php

return [
    'databases' => [
        'mysql' => [
            'host' => '127.0.0.1',
            'dbname' => 'mysql_test_db',
            'user' => 'root',
            'password' => '',
            'init' => 'CREATE DATABASE mysql_test_db;'
        ],
        'pgsql' => [
            'host' => '127.0.0.1',
            'dbname' => 'pgsql_test_db',
            'user' => 'postgres',
            'password' => '',
            'init' => 'CREATE DATABASE pgsql_test_db;'
        ],
    ]
];
