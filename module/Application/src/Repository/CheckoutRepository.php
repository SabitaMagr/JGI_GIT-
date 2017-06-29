<?php

namespace Application\Repository;

use Application\Model\Model;
use Zend\Db\Adapter\AdapterInterface;

class CheckoutRepository implements RepositoryInterface {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        
    }

    public function fetchEmployeeShfitDetails($employeeId) {
        $sql = "SELECT TO_CHAR(SYSDATE, 'HH24:MI:SS') AS CURRENT_TIME,
            TO_CHAR(S.END_TIME, 'HH24:MI:SS') AS CHECKOUT_TIME, 
            ESA.*,S.* FROM HRIS_EMPLOYEES E
                join HRIS_EMPLOYEE_SHIFT_ASSIGN ESA on (ESA.EMPLOYEE_ID=E.EMPLOYEE_ID)
                JOIN HRIS_SHIFTS S ON (S.SHIFT_ID=ESA.SHIFT_ID)
                WHERE E.EMPLOYEE_ID=$employeeId AND ESA.STATUS='E' AND ESA.MODIFIED_DT IS NULL
                AND (TO_DATE(TRUNC(SYSDATE), 'DD-MON-YY') BETWEEN TO_DATE(S.START_DATE, 'DD-MON-YY') AND TO_DATE(S.END_DATE, 'DD-MON-YY'))";
        
        $statement=$this->adapter->query($sql);
        $result=$statement->execute();
        return $result->current();
    }
    
    

}
