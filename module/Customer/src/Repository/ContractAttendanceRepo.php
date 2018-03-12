<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\ContractAttendanceModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ContractAttendanceRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(ContractAttendanceModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
//        $this->gateway->update($model->getArrayCopyForDB(), [CustomerContract::CONTRACT_ID => $id]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(ContractAttendanceModel::class, NULL, [
                    ContractAttendanceModel::ATTENDANCE_DT,
                        ], [ContractAttendanceModel::IN_TIME, ContractAttendanceModel::OUT_TIME], NUll, NULL, 'CA', NULL, NUll, [ContractAttendanceModel::TOTAL_HOUR]
                ), false);

        $select->from(['CA' => ContractAttendanceModel::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "CA." . ContractAttendanceModel::EMPLOYEE_ID . "=E.EMPLOYEE_ID", ['FULL_NAME' => new Expression("INITCAP(E.FULL_NAME)")], 'left');



        $select->where(['CA.' . ContractAttendanceModel::CONTRACT_ID => $id]);
        $select->order("CA.ATTENDANCE_DT ASC");

        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        return $result;
    }

    public function updateImportAttendance(Model $model, $contractId, $monthId, $employeeId, $attendanceDate) {
        $updateArray = [
            ContractAttendanceModel::CONTRACT_ID => $contractId,
            ContractAttendanceModel::MONTH_CODE_ID => $monthId,
            ContractAttendanceModel::EMPLOYEE_ID => $employeeId,
            ContractAttendanceModel::ATTENDANCE_DT => $attendanceDate
        ];

        $tempArray = $model->getArrayCopyForDB();
//        if (!array_key_exists('IN_TIME', $tempArray) || !array_key_exists('OUT_TIME', $tempArray) || !array_key_exists('TOTAL_HOUR', $tempArray)) {
//            $tempArray['IN_TIME'] = null;
//            $tempArray['OUT_TIME'] = null;
//            $tempArray['TOTAL_HOUR'] = null;
//        }

        $this->gateway->update($tempArray, $updateArray);
    }

    public function fetchContractAttendanceMonthWise($id, $monthId) {

        $sql = "SELECT CA.CONTRACT_ID AS CONTRACT_ID,
                INITCAP(TO_CHAR(CA.ATTENDANCE_DT, 'DD-MON-YYYY')) AS ATTENDANCE_DT,
                INITCAP(TO_CHAR(CA.IN_TIME, 'HH:MI AM')) AS IN_TIME,
                INITCAP(TO_CHAR(CA.OUT_TIME, 'HH:MI AM')) AS OUT_TIME, 
                NVL2(CA.TOTAL_HOUR,LPAD(TRUNC(CA.TOTAL_HOUR/60,0),2, 0)||':'||LPAD(MOD(CA.TOTAL_HOUR,60),2, 0),null) AS TOTAL_HOUR,
                CA.EMPLOYEE_ID AS EMPLOYEE_ID,
                NVL2(CA.NORMARL_HOUR,LPAD(TRUNC(CA.NORMARL_HOUR/60,0),2, 0)||':'||LPAD(MOD(CA.NORMARL_HOUR,60),2, 0),null) AS NORMARL_HOUR,
                NVL2(CA.PT_HOUR,LPAD(TRUNC(CA.PT_HOUR/60,0),2, 0)||':'||LPAD(MOD(CA.PT_HOUR,60),2, 0),null) AS PT_HOUR,
                NVL2(CA.OT_HOUR,LPAD(TRUNC(CA.OT_HOUR/60,0),2, 0)||':'||LPAD(MOD(CA.OT_HOUR,60),2, 0),null) AS OT_HOUR,
                CA.MONTH_CODE_ID AS MONTH_CODE_ID,
                (CASE CA.IS_ABSENT WHEN 'Y' THEN 'ABSENT' WHEN 'N' THEN 'PRESENT' ELSE '-' END) AS IS_ABSENT,
                (CASE CA.IS_SUBSTITUTE WHEN 'Y' THEN 'SUBSTITUTE' ELSE 'REGULAR' END) AS IS_SUBSTITUTE,
                INITCAP(SE.FULL_NAME) AS FULL_NAME,
                INITCAP(C.CONTRACT_NAME) AS CONTRACT_NAME 
                FROM HRIS_CUST_CONTRACT_ATTENDANCE CA 
                LEFT JOIN HRIS_SERVICE_EMPLOYEES SE ON CA.EMPLOYEE_ID=SE.EMPLOYEE_ID 
                LEFT JOIN HRIS_CUSTOMER_CONTRACT C ON C.CONTRACT_ID=CA.CONTRACT_ID 
                WHERE CA.CONTRACT_ID = {$id} AND CA.MONTH_CODE_ID = {$monthId} ORDER BY CA.ATTENDANCE_DT ASC";


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function deleteSubEmplooyee($conditon) {
        $this->gateway->delete($conditon);
    }

    public function getMonthList() {
        $sql = <<<EOT
            SELECT AM.MONTH_ID,M.MONTH_EDESC FROM
            (SELECT  UNIQUE (SELECT M.MONTH_ID
                FROM HRIS_MONTH_CODE M
                WHERE AD.ATTENDANCE_DT BETWEEN M.FROM_DATE AND M.TO_DATE
                ) AS MONTH_ID
            FROM HRIS_ATTENDANCE_DETAIL AD) AM JOIN HRIS_MONTH_CODE M ON (M.MONTH_ID=AM.MONTH_ID) 
EOT;
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function populateEmpAttendance($monthId, $customerId) {
        $sql = "BEGIN HRIS_GARDU_ATTENDNACE_MONTHLY({$monthId},{$customerId});  END;";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }

    public function getCutomerEmpAttendnaceMonthly($monthId, $customerId) {

        $pivotString = '';
        for ($i = 1; $i <= 32; $i++) {
            if ($i != 32) {
                $pivotString .= $i . ' AS ' . 'C' . $i . ', ';
            } else {
                $pivotString .= $i . ' AS ' . 'C' . $i;
            }
        }

        $sql = "
                select * from (select D.FROM_DATE,D.DAY_COUNT,
 CE.EMPLOYEE_ID,E.FULL_NAME,CE.CONTRACT_ID,CE.LOCATION_ID,CL.LOCATION_NAME,CD.SHIFT_ID,
 CASE  WHEN CA.STATUS IS NULL THEN 
 'PR'
 ELSE CA.STATUS END AS STATUS
 from (SELECT   M.FROM_DATE + ROWNUM -1  AS DATES,ROWNUM AS DAY_COUNT,M.FROM_DATE
    FROM dual d
    join HRIS_MONTH_CODE M on (1=1) where m.month_id={$monthId}
    CONNECT BY  rownum <= M.TO_DATE - M.FROM_DATE + 1
 ) D
   LEFT JOIN HRIS_CUST_CONTRACT_EMP CE on (1=1)
    LEFT JOIN HRIS_CONTRACT_EMP_ATTENDANCE CA ON (CA.EMPLOYEE_ID=CE.EMPLOYEE_ID 
    AND CA.LOCATION_ID=CE.LOCATION_ID AND CA.ATTENDANCE_DATE=D.DATES)
    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=CE.EMPLOYEE_ID)
    LEFT JOIN HRIS_CUSTOMER_LOCATION CL ON (CL.LOCATION_ID=CE.LOCATION_ID)
    LEFT JOIN HRIS_CUSTOMER_CONTRACT_DETAILS CD ON (CD.CONTRACT_ID=CE.CONTRACT_ID AND CD.DESIGNATION_ID=CE.DESIGNATION_ID)
    LEFT JOIN HRIS_SHIFTS S ON (S.SHIFT_ID=CD.SHIFT_ID)
    WHERE CE.STATUS='E' AND CE.CUSTOMER_ID={$customerId})PIVOT (MAX(STATUS) FOR DAY_COUNT IN ({$pivotString})) 
                ";

//    echo $sql;
//    die();


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function updateAttendance($sql) {
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
