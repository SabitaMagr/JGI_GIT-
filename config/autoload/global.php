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

// 'db' => [
//     'driver'    => 'oci',
//     'dsn'       => 'oci:dbname=(DESCRIPTION =
//         (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.2)(PORT = 1521))
//         (CONNECT_DATA =
//             (SERVER = DEDICATED)
//             (SERVICE_NAME = ITN)
//         )
//       )',
//     'username'      => 'HRIS',
//     'password'      => 'NEO-HRIS',
//     'platform_options' => ['quote_identifiers' => false]
// ],


// $dbParams = [
//     'hostname' => 'localhost',
//     'username' => 'root',
//     'password' => 'root',
//     'database' => 'hr'
// ];



return [

//     'doctrine' => [
//         'connection' => [
//             'orm_default' => [
//                 'params' => [
//                     'host' => $dbParams['hostname'],
//                     'user' => $dbParams['username'],
//                     'password' => $dbParams['password'],
//                     'dbname' => $dbParams['database'],
//                     'driverOptions' => [
//                         PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
//                     ],
//                 ],
//             ],
//         ],
//     ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],


];



