<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;

class PayrollReportRepo implements RepositoryInterface {

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

    public function getVarianceColumns() {
        $data['previous'] = $this->varianceColumnsPre();
        $data['current'] = $this->varianceColumnsCur();
        $data['difference'] = $this->varianceColumnsDif();
        return $data;
    }

    public function varianceColumnsPre() {
        $sql = "select 'V'||Variance_Id||'_P'  as VARIANCE,VARIANCE_NAME from Hris_Variance where status='E' and Show_Default='Y'";
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function varianceColumnsCur() {
        $sql = "select 'V'||Variance_Id||'_C'  as VARIANCE,VARIANCE_NAME from Hris_Variance where status='E' and Show_Default='Y'";
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function varianceColumnsDif() {
        $sql = "select 'V'||Variance_Id||'_D'  as VARIANCE,VARIANCE_NAME from Hris_Variance where status='E' and Show_Default='Y' and Show_Difference='Y'";
        $data = EntityHelper::rawQueryResult($this->adapter, $sql);
        return Helper::extractDbData($data);
    }

    public function getVarianceReprot($data) {
        $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
        $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
        $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
        $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
        $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
        $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
        $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
        $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
        $genderId = isset($data['genderId']) ? $data['genderId'] : -1;
        $functionalTypeId = isset($data['functionalTypeId']) ? $data['functionalTypeId'] : -1;
        $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
        $monthId = $data['monthId'];

        $searchConditon = EntityHelper::getSearchConditon($companyId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $employeeTypeId, $employeeId, $genderId, null, $functionalTypeId);

        $sql = "SELECT 
E.FULL_NAME,
E.EMPLOYEE_CODE
,D.DEPARTMENT_NAME
,FUNT.FUNCTIONAL_TYPE_EDESC
,AD.CUR_ADDRESS
,AD.CUR_ACCOUNT
,AD.PRE_ADDRESS
,AD.PRE_ACCOUNT
,AD.ADDRESS_REMARKS
,AD.ACCOUNT_REMARKS
,VARY.*
FROM (SELECT 
*
FROM 
(SELECT 
C.EMPLOYEE_ID
,C.VARIANCE_ID
,C.TOTAL AS C_TOTAL
,P.TOTAL AS P_TOTAL
,P.Total-C.TOTAL AS DIFFERENCE
FROM (SELECT 
SD.EMPLOYEE_ID
,Vp.Variance_Id
,SS.Month_ID
,V.Show_Difference
,SUM(VAL) AS TOTAL
FROM HRIS_VARIANCE V
LEFT JOIN HRIS_VARIANCE_PAYHEAD VP ON (V.VARIANCE_ID=VP.VARIANCE_ID)
LEFT JOIN (select * from HRIS_SALARY_SHEET where month_id={$monthId}) SS ON (1=1)
LEFT JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO=SD.SHEET_NO AND SD.Pay_Id=VP.Pay_Id)
WHERE V.SHOW_DEFAULT='Y' AND V.STATUS='E'
GROUP BY  SD.EMPLOYEE_ID,V.VARIANCE_NAME,Vp.Variance_Id,SS.Month_ID,V.Show_Difference) C
LEFT JOIN 
(
SELECT 
SD.EMPLOYEE_ID
,Vp.Variance_Id
,SS.Month_ID
,V.Show_Difference
,SUM(VAL) AS TOTAL
FROM HRIS_VARIANCE V
LEFT JOIN HRIS_VARIANCE_PAYHEAD VP ON (V.VARIANCE_ID=VP.VARIANCE_ID)
LEFT JOIN (select * from HRIS_SALARY_SHEET where 
month_id=(SELECT MONTH_ID FROM  HRIS_MONTH_CODE WHERE TO_DATE=(SELECT FROM_DATE-1 FROM HRIS_MONTH_CODE WHERE MONTH_ID={$monthId}))
) SS ON (1=1)
LEFT JOIN HRIS_SALARY_SHEET_DETAIL SD ON (SS.SHEET_NO=SD.SHEET_NO AND SD.Pay_Id=VP.Pay_Id)
WHERE V.SHOW_DEFAULT='Y' AND V.STATUS='E'
GROUP BY  SD.EMPLOYEE_ID,V.VARIANCE_NAME,Vp.Variance_Id,SS.Month_ID,V.Show_Difference) P ON
(C.EMPLOYEE_ID=P.EMPLOYEE_ID AND C.VARIANCE_ID=P.VARIANCE_ID ))
PIVOT ( MAX( C_TOTAL ) AS C ,MAX( P_TOTAL ) AS P ,MAX( DIFFERENCE ) AS D
    FOR Variance_Id 
    IN ( '1' AS V1,'2' AS V2)
    )
    ) 
    VARY
    LEFT JOIN (SELECT
CUR.EMPLOYEE_ID
,CUR.PERMANENT_ADDRESS AS CUR_ADDRESS
,CUR.ACCOUNT_NO AS CUR_ACCOUNT
,PREV.PERMANENT_ADDRESS AS PRE_ADDRESS
,PREV.ACCOUNT_NO AS PRE_ACCOUNT
,CASE WHEN CUR.PERMANENT_ADDRESS!=PREV.PERMANENT_ADDRESS
THEN
'Changed'
ELSE
'Not Changed'
END as ADDRESS_REMARKS
,CASE WHEN CUR.ACCOUNT_NO!=PREV.ACCOUNT_NO
THEN
'Changed'
ELSE
'Not Changed'
END as ACCOUNT_REMARKS
FROM 
(select * from HRIS_SALARY_SHEET SSC
left join HRIS_SALARY_SHEET_EMP_DETAIL SEDC ON (SEDC.SHEET_NO=SSC.SHEET_NO)
where SSC.month_id={$monthId}) CUR
LEFT JOIN (select * from HRIS_SALARY_SHEET SSP
left join HRIS_SALARY_SHEET_EMP_DETAIL SEDP ON (SEDP.SHEET_NO=SSP.SHEET_NO)
where SSP.month_id=(SELECT MONTH_ID FROM  HRIS_MONTH_CODE WHERE TO_DATE=(SELECT FROM_DATE-1 FROM HRIS_MONTH_CODE WHERE MONTH_ID={$monthId})))
PREV ON (CUR.EMPLOYEE_ID=PREV.EMPLOYEE_ID))  AD ON (AD.EMPLOYEE_ID=VARY.EMPLOYEE_ID)
    LEFT JOIN HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=VARY.EMPLOYEE_ID)
    LEFT JOIN HRIS_DEPARTMENTS D  ON (D.DEPARTMENT_ID=E.DEPARTMENT_ID)
    LEFT JOIN HRIS_FUNCTIONAL_TYPES FUNT ON (E.FUNCTIONAL_TYPE_ID=FUNT.FUNCTIONAL_TYPE_ID)
WHERE 1=1 {$searchConditon}
";
        return EntityHelper::rawQueryResult($this->adapter, $sql);
    }

}
