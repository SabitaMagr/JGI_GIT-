<?php
$dbParams1 = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'root',
    'database' => 'hr'
];
return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'host' => $dbParams1['hostname'],
                    'user' => $dbParams1['username'],
                    'password' => $dbParams1['password'],
                    'dbname' => $dbParams1['database'],
                    'driverOptions' => [
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                    ],
                ],
            ],
        ],
    ],
];