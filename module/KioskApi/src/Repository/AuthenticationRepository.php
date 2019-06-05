<?php

namespace KioskApi\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class AuthenticationRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchEmployeeData($thumbId) {
        $sql = "
            SELECT EMPLOYEE_ID,
            FULL_NAME 
            FROM HRIS_EMPLOYEES WHERE ID_THUMB_ID = {$thumbId}
            ";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDBData($result);
    }

}
