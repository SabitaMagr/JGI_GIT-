<?php

return [
    'db' => [
        'driver' => 'oci8',
        'connection_string' => '(DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.2)(PORT = 1521))
        (CONNECT_DATA =
        (SERVER = DEDICATED)
        (SERVICE_NAME = ITN)
        )
        )',
        
    'username' => 'HRIS',
    'password' => 'NEO_HRIS',
          
//        'username' => 'LAXMI_HRIS_APR28',
//        'password' => 'LAXMI_HRIS_APR28',
        
//          'username' => 'HRIS_DEMO',
//          'password' => 'HRIS_DEMO',
        
//        'username' => 'JWL_HRIS_APR5',
//        'password' => 'JWL_HRIS_APR5',
//        
//        'username' => 'JWL_HRIS_APR4',
//        'password' => 'JWL_HRIS_APR4',
//        
     //   'username' => 'ITNEPAL_HRIS_APR2',
      //  'password' => 'ITNEPAL_HRIS_APR2',
        
//        'username'      => 'HRIS_JWL',
//        'password'      => 'HRIS_JWL',
//        
//        'username' => 'HRIS_MODERN',
//        'password' => 'HRIS_MODERN',
        
   //     'username'=>'HRIS_VIANET',
     //   'password'=>'HRIS_VIANET',
        
        'platform_options' => ['quote_identifiers' => false]
    ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
];
