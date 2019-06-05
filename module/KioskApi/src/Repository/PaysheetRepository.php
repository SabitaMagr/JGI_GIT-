<?php

namespace KioskApi\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class PaysheetRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchPaysheet($employeeId) {
        $sql = "
           select * from HRIS_SALARY_SHEET_EMP_DETAIL
           where employee_id = {$employeeId}
               and end_date = (select max(end_date) from HRIS_SALARY_SHEET_EMP_DETAIL
                                    where employee_id={$employeeId})
            ";
            
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
