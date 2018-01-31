<?php

namespace SelfService\Repository;

use Application\Repository\HrisRepository;
use LeaveManagement\Model\LeaveAssign;
use Traversable;
use Zend\Db\Adapter\AdapterInterface;

class LeaveRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName == null) {
            $tableName = LeaveAssign::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
    }

    function selectAll($employeeId): Traversable {
        $sql = "SELECT LA.LEAVE_ID,
                  LMS.LEAVE_CODE,
                  LMS.LEAVE_ENAME,
                  LA.TOTAL_DAYS,
                  LA.BALANCE,
                  (SELECT SUM(ELR.NO_OF_DAYS/(
                    CASE
                      WHEN ELR.HALF_DAY IN ('F','S')
                      THEN 2
                      ELSE 1
                    END))
                  FROM HRIS_EMPLOYEE_LEAVE_REQUEST ELR
                  WHERE ELR.LEAVE_ID =LA.LEAVE_ID
                  AND ELR.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELR.STATUS     ='AP'
                  ) AS LEAVE_TAKEN,
                  (SELECT SUM(EPD.NO_OF_DAYS)
                  FROM HRIS_EMPLOYEE_PENALTY_DAYS EPD
                  WHERE EPD.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND EPD.LEAVE_ID     =LA.LEAVE_ID
                  ) AS LEAVE_DEDUCTED,
                  (SELECT SUM(ELA.NO_OF_DAYS)
                  FROM HRIS_EMPLOYEE_LEAVE_ADDITION ELA
                  WHERE ELA.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELA.LEAVE_ID     =LA.LEAVE_ID
                  ) AS LEAVE_ADDED
                FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS
                ON (LA.LEAVE_ID     =LMS.LEAVE_ID)
                WHERE LA.EMPLOYEE_ID={$employeeId} AND LMS.STATUS ='E' AND LMS.IS_MONTHLY = 'N' ORDER BY LMS.LEAVE_ENAME ASC";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

    function monthlyLeaveStatus($employeeId, $fiscalYearMonthNo) {
        $sql = "SELECT LA.LEAVE_ID,
                  LMS.LEAVE_CODE,
                  LMS.LEAVE_ENAME,
                  LA.TOTAL_DAYS,
                  LA.BALANCE,
                  (SELECT SUM(ELR.NO_OF_DAYS)
                  FROM HRIS_EMPLOYEE_LEAVE_REQUEST ELR
                  WHERE ELR.LEAVE_ID =LA.LEAVE_ID
                  AND ELR.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELR.STATUS     ='AP'
                  ) AS LEAVE_TAKEN
                FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS
                ON (LA.LEAVE_ID     =LMS.LEAVE_ID)
                WHERE LA.EMPLOYEE_ID={$employeeId}
                AND LA.FISCAL_YEAR_MONTH_NO ={$fiscalYearMonthNo}
                AND LMS.STATUS     ='E'
                AND LMS.IS_MONTHLY = 'Y'
                ORDER BY LMS.LEAVE_ENAME ASC;";
        $statement = $this->adapter->query($sql);
        return $statement->execute();
    }

}
