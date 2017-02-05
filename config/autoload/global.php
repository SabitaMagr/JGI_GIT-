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
//return [
    //  'db' => [
    //     'driver' => 'Mysqli',
    //     'database' => 'album',
    //     'host'=> 'localhost',
    //     'username' => 'root',
    //     'password' => 'root',
    // ],

    // 'service_manager' => [
    //     'factories' => [
    //         'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
    //     ],
    // ],

return [
    'db' => [
        'driver'    => 'oci8',
        'connection_string'       => '(DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
        (CONNECT_DATA =
        (SERVER = DEDICATED)
        (SERVICE_NAME = ORCLS)
        )
        )',
        'username'      => 'HRIS_NEO_FINAL',
        'password'      => 'HRIS_NEO_FINAL',
        'platform_options' => ['quote_identifiers' => false]
    ],
    
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
];