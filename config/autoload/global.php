<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
   'db' => [
       'driver' => 'Pdo',
       'dsn'    => sprintf('sqlite:%s/data/schema.db', realpath(getcwd())),
   ],

//    'db' => [
//        'driver'         => 'Pdo',
//        'dsn'            => 'mysql:dbname=zf2tutorial;host=127.0.0.1',
//        'driver_options' => [
//            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
//        ],
//    ],

//     'db' => [
//         'driver'    => 'oci',
//         'dsn'       => 'oci:dbname=(DESCRIPTION =
//             (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.2)(PORT = 1521))
//             (CONNECT_DATA =
//                 (SERVER = DEDICATED)
//                 (SERVICE_NAME = erp)
//             )
//           )',
//         'username'      => 'distribution',
//         'password'      => 'DISTRIBUTION',
//         'platform_options' => ['quote_identifiers' => false]
//     ],

    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
];
