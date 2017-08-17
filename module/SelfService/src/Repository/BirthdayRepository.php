<?php

namespace SelfService\Repository;

use SelfService\Model\BirthdayModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class BirthdayRepository {
    
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(BirthdayModel::TABLE_NAME,$adapter);
    }

    public function getBirthdays() {
        $sql = "SELECT * FROM (
                                SELECT EMP.EMPLOYEE_ID,
                                  ( CASE
                                      WHEN EMP.MIDDLE_NAME IS NULL THEN EMP.FIRST_NAME || ' ' || EMP.LAST_NAME
                                      ELSE EMP.FIRST_NAME || ' ' || EMP.MIDDLE_NAME || ' ' || EMP.LAST_NAME
                                  END ) FULL_NAME, 
                                  DSG.DESIGNATION_TITLE,
                                  EFL.FILE_PATH,
                                  EMP.BIRTH_DATE,
                                  TO_CHAR(EMP.BIRTH_DATE, 'fmddth Month') EMP_BIRTH_DATE, 
                                  'TODAY' BIRTHDAYFOR
                                FROM HRIS_EMPLOYEES EMP, HRIS_DESIGNATIONS DSG, HRIS_EMPLOYEE_FILE EFL
                                WHERE TO_CHAR(EMP.BIRTH_DATE, 'MMDD') = TO_CHAR(SYSDATE,'MMDD')
                                AND EMP.RETIRED_FLAG = 'N'
                                AND EMP.DESIGNATION_ID = DSG.DESIGNATION_ID
                                AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
                                UNION ALL
                                SELECT EMP.EMPLOYEE_ID,
                                  ( CASE
                                      WHEN EMP.MIDDLE_NAME IS NULL THEN EMP.FIRST_NAME || ' ' || EMP.LAST_NAME
                                      ELSE EMP.FIRST_NAME || ' ' || EMP.MIDDLE_NAME || ' ' || EMP.LAST_NAME
                                  END ) FULL_NAME, 
                                  DSG.DESIGNATION_TITLE,
                                  EFL.FILE_PATH,
                                  EMP.BIRTH_DATE,
                                  TO_CHAR(EMP.BIRTH_DATE, 'fmddth Month') EMP_BIRTH_DATE, 
                                  'UPCOMING' BIRTHDAYFOR
                                FROM HRIS_EMPLOYEES EMP, HRIS_DESIGNATIONS DSG, HRIS_EMPLOYEE_FILE EFL
                                WHERE TO_CHAR(EMP.BIRTH_DATE, 'MMDD') > TO_CHAR(SYSDATE,'MMDD')
                                AND EMP.RETIRED_FLAG = 'N'
                                AND EMP.DESIGNATION_ID = DSG.DESIGNATION_ID
                                AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
                ) ORDER BY TO_CHAR(BIRTH_DATE,'MMDD')
                ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
         $birthdayResult = array();
        foreach ($result as $rs) {
            if ('TODAY' == strtoupper($rs['BIRTHDAYFOR'])) {
                $birthdayResult['TODAY'][$rs['EMPLOYEE_ID']] = $rs;
            }
            if ('UPCOMING' == strtoupper($rs['BIRTHDAYFOR'])) {
                $birthdayResult['UPCOMING'][$rs['EMPLOYEE_ID']] = $rs;
            }
        }

        return $birthdayResult;
    }
    
    public function add($model){
          $this->tableGateway->insert($model->getArrayCopyForDB());
    }

       
    }


