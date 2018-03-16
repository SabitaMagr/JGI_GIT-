<?php

namespace Customer\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Customer\Model\ContractAbsentDetailsModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class ContractAbsentDetailsRepo implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(ContractAbsentDetailsModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->gateway->update([ContractAbsentDetailsModel::STATUS => EntityHelper::STATUS_DISABLED], [ContractAbsentDetailsModel::ID => $id]);
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [ContractAbsentDetailsModel::ID => $id]);
    }

    public function fetchAll() {
        $sql = "  SELECT ES.ID,TO_CHAR(ES.ATTENDANCE_DATE,'DD-MON-YYYY') AS ATTENDANCE_DATE_AD,
                  BS_DATE(ES.ATTENDANCE_DATE)               AS ATTENDANCE_DATE_BS,
                  E.FULL_NAME AS EMPLOYEE_NAME,
                  C.CUSTOMER_ENAME,
                  CC.CONTRACT_NAME,
                  CL.LOCATION_NAME,
                  S.SHIFT_ENAME,
                  SE.FULL_NAME AS SUB_EMPLOYEE_NAME,
                  CASE ES.POSTING_TYPE 
                  WHEN 'SU' THEN 'Substitute'
                  WHEN 'OT' THEN 'Over Time'
                  WHEN 'PT' THEN 'Part Time'
                  END  AS POSTING_TYPE
                  FROM HRIS_CONTRACT_EMP_ABSENT_SUB ES
                  LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=ES.EMPLOYEE_ID)
                  LEFT JOIN HRIS_CUSTOMER C ON (C.CUSTOMER_ID=ES.CUSTOMER_ID)
                  LEFT JOIN HRIS_CUSTOMER_CONTRACT CC ON (CC.CONTRACT_ID=ES.CONTRACT_ID)
                  LEFT JOIN HRIS_CUSTOMER_LOCATION CL ON (CL.LOCATION_ID=ES.EMPLOYEE_LOCATION_ID)
                  LEFT JOIN HRIS_SHIFTS S ON (S.SHIFT_ID=ES.EMPLOYEE_SHIFT_ID)
                  LEFT JOIN HRIS_EMPLOYEES SE ON (SE.EMPLOYEE_ID=ES.SUB_EMPLOYEE_ID)
                  where ES.STATUS='E'
                  ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $columns = EntityHelper::getColumnNameArrayWithOracleFns(ContractAbsentDetailsModel::class, NULL, [
                    ContractAbsentDetailsModel::ATTENDANCE_DATE,
                        ], NULL, NULL, NULL, 'AD');
        $select->columns($columns, false);

        $select->from(['AD' => ContractAbsentDetailsModel::TABLE_NAME]);
//                ->join(['BG' => "HRIS_BLOOD_GROUPS"], "WE." . ServiceEmployeeSetupModel::BLOOD_GROUP_ID . "=BG.BLOOD_GROUP_ID", ['BLOOD_GROUP_CODE'], 'left');

        $select->where([
            "AD.STATUS='E'"
        ]);
        $select->where([
            "AD.ID=$id"
        ]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllEmployeeDetails($employeeId, $contractId) {
        if ($contractId) {
            $sql = "SELECT E.DESIGNATION_ID,D.DESIGNATION_TITLE,CD.SHIFT_ID,S.SHIFT_ENAME,CE.* 
                FROM HRIS_CUST_CONTRACT_EMP CE
               LEFT JOIN HRIS_CUSTOMER_CONTRACT_DETAILS CD ON (CE.CONTRACT_ID=CD.CONTRACT_ID AND CD.DESIGNATION_ID=CE.DESIGNATION_ID)
               LEFT JOIN HRIS_EMPLOYEES E ON (CE.EMPLOYEE_ID=E.EMPLOYEE_ID)
               LEFT  JOIN HRIS_DESIGNATIONS D ON (CE.DESIGNATION_ID=D.DESIGNATION_ID)
               LEFT JOIN HRIS_SHIFTS S ON (S.SHIFT_ID=CD.SHIFT_ID)
                WHERE CE.EMPLOYEE_ID={$employeeId} AND CE.CONTRACT_ID={$contractId}";
        } else {
            $sql = "SELECT E.DESIGNATION_ID,D.DESIGNATION_TITLE FROM HRIS_EMPLOYEES E
                JOIN HRIS_DESIGNATIONS D ON (E.DESIGNATION_ID=D.DESIGNATION_ID)
                WHERE E.EMPLOYEE_ID={$employeeId}";
        }



        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

}
