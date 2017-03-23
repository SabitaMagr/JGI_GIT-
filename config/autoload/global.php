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
/* remote config commented if incase replaced during merge */
//    'db' => [
//        'driver'    => 'oci8',
//        'connection_string'       => '(DESCRIPTION =
//        (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.2)(PORT = 1521))
//        (CONNECT_DATA =
//        (SERVER = DEDICATED)
//        (SERVICE_NAME = ITN)
//        )
//        )',
//        'username'      => 'HRIS',
//        'password'      => 'NEO_HRIS',
//        'platform_options' => ['quote_identifiers' => false]
//    ],

return [
//    'db' => [
//        'driver'    => 'oci8',
//        'connection_string'       => '(DESCRIPTION =
//        (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.2)(PORT = 1521))
//        (CONNECT_DATA =
//        (SERVER = DEDICATED)
//        (SERVICE_NAME = ITN)
//        )
//        )',
//        'username'      => 'HRIS_JWL',
//        'password'      => 'HRIS_JWL',
//        'platform_options' => ['quote_identifiers' => false]
//    ],
    
           'db' => [
        'driver'    => 'oci8',
        'connection_string'       => '(DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.2)(PORT = 1521))
        (CONNECT_DATA =
        (SERVER = DEDICATED)
        (SERVICE_NAME = ITN)
        )
        )',
        'username'      => 'HRIS',
        'password'      => 'NEO_HRIS',
        'platform_options' => ['quote_identifiers' => false]
    ],
    
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
];