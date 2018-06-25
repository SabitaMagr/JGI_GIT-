<?php
namespace LeaveManagement\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use LeaveManagement\Model\LeaveAssign;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;

class LeaveAssignRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName == null) {
            $tableName = LeaveAssign::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [LeaveAssign::LEAVE_ID => $id[0], LeaveAssign::EMPLOYEE_ID => $id[1]]);
        EntityHelper::rawQueryResult($this->adapter, "
            BEGIN
              HRIS_RECALCULATE_LEAVE({$id[1]},{$id[0]});
            END;");
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function fetchByEmployeeId($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['A' => LeaveAssign::TABLE_NAME])
            ->join(['S' => 'HRIS_LEAVE_MASTER_SETUP'], 'A.LEAVE_ID=S.LEAVE_ID');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function filter($branchId, $departmentId, $genderId, $designationId, $serviceTypeId, $employeeId, $companyId, $positionId, $employeeTypeId, $leaveId): array {
        $searchCondition = EntityHelper::getSearchConditon($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, null, $employeeTypeId, $employeeId, $genderId);
        $sql = "SELECT C.COMPANY_NAME,
                  B.BRANCH_NAME,
                  DEP.DEPARTMENT_NAME,
                  E.EMPLOYEE_ID,
                  E.EMPLOYEE_CODE,
                  E.FULL_NAME,
                  ELA.LEAVE_ID,
                  ELA.PREVIOUS_YEAR_BAL,
                  ELA.TOTAL_DAYS,
                  ELA.BALANCE,
                  LS.IS_MONTHLY,
                  MC.MONTH_EDESC
                FROM HRIS_EMPLOYEES E
                LEFT JOIN (SELECT * FROM HRIS_EMPLOYEE_LEAVE_ASSIGN WHERE LEAVE_ID   ={$leaveId}) ELA
                ON (E.EMPLOYEE_ID = ELA.EMPLOYEE_ID)
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP LS
                ON(LS.LEAVE_ID={$leaveId})
                LEFT JOIN HRIS_MONTH_CODE MC 
                ON (MC.FISCAL_YEAR_ID=LS.FISCAL_YEAR AND MC.FISCAL_YEAR_MONTH_NO=ELA.FISCAL_YEAR_MONTH_NO)
                LEFT JOIN HRIS_COMPANY C
                ON (E.COMPANY_ID=C.COMPANY_ID)
                LEFT JOIN HRIS_BRANCHES B
                ON (E.BRANCH_ID=B.BRANCH_ID)
                LEFT JOIN HRIS_DEPARTMENTS DEP
                ON (E.DEPARTMENT_ID=DEP.DEPARTMENT_ID)
                WHERE 1            =1 
                {$searchCondition}
                ORDER BY C.COMPANY_NAME,B.BRANCH_NAME,DEP.DEPARTMENT_NAME,E.FULL_NAME,MC.FISCAL_YEAR_MONTH_NO";
                
//                echo $sql;
//                die();

        return $this->rawQuery($sql);
    }

    public function filterByLeaveEmployeeId($leaveId, $employeeId) {
        $result = $this->tableGateway->select([LeaveAssign::LEAVE_ID => $leaveId, LeaveAssign::EMPLOYEE_ID => $employeeId]);
        return $result->current();
    }

    public function fetchById($id) {
        $rowset = $this->tableGateway->select([LeaveAssign::EMPLOYEE_LEAVE_ASSIGN_ID => $id]);
        return $rowset->current();
    }

    public function delete($id) {
        $this->tableGateway->delete([LeaveAssign::EMPLOYEE_LEAVE_ASSIGN_ID => $id]);
    }

    public function updatePreYrBalance($employeeId, $leaveId, $preYrBalance, $totalDays, $balance) {
        $this->tableGateway->update([LeaveAssign::PREVIOUS_YEAR_BAL => $preYrBalance, LeaveAssign::TOTAL_DAYS => $totalDays, LeaveAssign::BALANCE => $balance], [LeaveAssign::EMPLOYEE_ID => $employeeId, LeaveAssign::LEAVE_ID => $leaveId]);
    }
    
    public function addMonthlyLeave($employeeId,$leaveDetails,$monthId,$totalDays=null){
        $monthlyDays=($totalDays !=null && $totalDays !=0)?$totalDays:$leaveDetails['DEFAULT_DAYS'];
        $sql="DECLARE
            V_DEFAULT_LEAVE_DAYS NUMBER:={$monthlyDays};
            V_LEAVE_ID NUMBER:={$leaveDetails['LEAVE_ID']};
            V_MONTH_ID NUMBER:={$monthId};
     V_COUNT NUMBER;
     V_FISCAL_YEAR_ID HRIS_FISCAL_YEARS.FISCAL_YEAR_ID%TYPE:={$leaveDetails['FISCAL_YEAR']};
     V_EMPLOYEE_ID NUMBER:={$employeeId};
         V_MONTH_COUNT NUMBER:=1;
    BEGIN

    FOR i IN V_MONTH_ID..12
            LOOP
              SELECT COUNT(*)
              INTO V_COUNT
              FROM HRIS_EMPLOYEE_LEAVE_ASSIGN
              WHERE EMPLOYEE_ID       =V_EMPLOYEE_ID
              AND LEAVE_ID            = V_LEAVE_ID
              AND FISCAL_YEAR_MONTH_NO=i ;
              IF ( V_COUNT            =0 )THEN
                INSERT
                INTO HRIS_EMPLOYEE_LEAVE_ASSIGN
                  (
                    EMPLOYEE_ID,
                    LEAVE_ID,
                    PREVIOUS_YEAR_BAL,
                    TOTAL_DAYS,
                    BALANCE,
                    FISCAL_YEAR,
                    FISCAL_YEAR_MONTH_NO,
                    CREATED_DT
                  )
                  VALUES
                  (
                    V_EMPLOYEE_ID,
                    V_LEAVE_ID,
                    0,
                    V_DEFAULT_LEAVE_DAYS*V_MONTH_COUNT,
                    V_DEFAULT_LEAVE_DAYS*V_MONTH_COUNT,
                    V_FISCAL_YEAR_ID,
                    i,
                    TRUNC(SYSDATE)
                  );
              END IF;
              
              V_MONTH_COUNT:=V_MONTH_COUNT+1;
            END LOOP;
        
        END;";
         $this->executeStatement($sql);
    }
    
    
    public function editMonthlyLeave($employeeId,$leaveDetails,$monthId,$totalDays=null){
        $monthlyDays=($totalDays !=null && $totalDays !=0)?$totalDays:$leaveDetails['DEFAULT_DAYS'];
        $sql="DECLARE
            V_DEFAULT_LEAVE_DAYS NUMBER:={$monthlyDays};
            V_LEAVE_ID NUMBER:={$leaveDetails['LEAVE_ID']};
            V_MONTH_ID NUMBER:={$monthId};
     V_COUNT NUMBER;
     V_FISCAL_YEAR_ID HRIS_FISCAL_YEARS.FISCAL_YEAR_ID%TYPE:={$leaveDetails['FISCAL_YEAR']};
     V_EMPLOYEE_ID NUMBER:={$employeeId};
         V_MONTH_COUNT NUMBER:=1;
    BEGIN
    
        DELETE  FROM HRIS_EMPLOYEE_LEAVE_ASSIGN WHERE 
        EMPLOYEE_ID=V_EMPLOYEE_ID 
        AND LEAVE_ID=V_LEAVE_ID;

    FOR i IN V_MONTH_ID..12
            LOOP
              
                INSERT
                INTO HRIS_EMPLOYEE_LEAVE_ASSIGN
                  (
                    EMPLOYEE_ID,
                    LEAVE_ID,
                    PREVIOUS_YEAR_BAL,
                    TOTAL_DAYS,
                    BALANCE,
                    FISCAL_YEAR,
                    FISCAL_YEAR_MONTH_NO,
                    CREATED_DT
                  )
                  VALUES
                  (
                    V_EMPLOYEE_ID,
                    V_LEAVE_ID,
                    0,
                    V_DEFAULT_LEAVE_DAYS*V_MONTH_COUNT,
                    V_DEFAULT_LEAVE_DAYS*V_MONTH_COUNT,
                    V_FISCAL_YEAR_ID,
                    i,
                    TRUNC(SYSDATE)
                  );
              
              
              V_MONTH_COUNT:=V_MONTH_COUNT+1;
            END LOOP;
        
        END;";
         $this->executeStatement($sql);
    }
    
    
}
