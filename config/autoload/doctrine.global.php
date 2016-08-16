<?php


//$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.XX.XXX)(PORT = 1521)))(CONNECT_DATA=(SID=XXXX)))"; 
$db       = '(DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.2)(PORT = 1521))
    (CONNECT_DATA =
    (SERVER = DEDICATED)
    (SERVICE_NAME = ITN)
    )
    )';

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\OCI8\Driver',
                'params' => [
                    'driver' => 'oci8',
                    'host'     => '192.168.4.2',
                    'port'     => '1521',
                    'user'     => 'HRIS',
                    'password' => 'NEO_HRIS',
                    'dbname'   => 'ITN',
                    'servicename'=>'ITN'
                    
                ],
            ],
        ],
    ],
];





   
