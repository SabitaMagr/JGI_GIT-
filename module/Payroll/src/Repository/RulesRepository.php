<?php

namespace Payroll\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\HrisRepository;
use Payroll\Model\Rules;
use Traversable;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;

class RulesRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        if ($tableName == null) {
            $tableName = Rules::TABLE_NAME;
        }
        parent::__construct($adapter, $tableName);
    }

    public function add(Model $model) {
        return $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $this->tableGateway->update($model->getArrayCopyForDB(), [Rules::PAY_ID => $id]);
    }

    public function fetchAll(): Traversable {
        $query = "SELECT PAY_ID,
                  PAY_CODE,
                  PAY_EDESC,
                  PAY_TYPE_FLAG,
                  (
                  CASE
                    WHEN PAY_TYPE_FLAG ='A'
                    THEN 'Additon'
                    WHEN PAY_TYPE_FLAG='D'
                    THEN 'Deduction'
                    WHEN PAY_TYPE_FLAG='V'
                    THEN 'View'
                    ELSE 'Tax'
                  END) AS PAY_TYPE,
                  PRIORITY_INDEX,
                  INCLUDE_IN_TAX,
                  (
                  CASE
                    WHEN INCLUDE_IN_TAX = 'Y'
                    THEN 'Yes'
                    ELSE 'No'
                  END ) AS INCLUDE_IN_TAX_DETAIL,
                  INCLUDE_IN_SALARY,
                  (
                  CASE
                    WHEN INCLUDE_IN_SALARY = 'Y'
                    THEN 'Yes'
                    ELSE 'No'
                  END ) AS INCLUDE_IN_SALARY_DETAIL,
                  INCLUDE_PAST_VALUE,
                  (
                  CASE
                    WHEN INCLUDE_PAST_VALUE = 'Y'
                    THEN 'Yes'
                    ELSE 'No'
                  END ) AS INCLUDE_PAST_VALUE_DETAIL,
                  INCLUDE_FUTURE_VALUE,
                  (
                  CASE
                    WHEN INCLUDE_FUTURE_VALUE = 'Y'
                    THEN 'Yes'
                    ELSE 'No'
                  END ) AS INCLUDE_FUTURE_VALUE_DETAIL,
                  FORMULA,
                  REMARKS,
                  STATUS
                FROM HRIS_PAY_SETUP
                WHERE STATUS ='E' ORDER BY PRIORITY_INDEX";

        $statement = $this->adapter->query($query);
        return $statement->execute();
    }

    public function fetchById($id) {
        return $this->tableGateway->select(function(Select $select) use($id) {
                    $select->where([Rules::STATUS => 'E', Rules::PAY_ID => $id]);
                })->current();
    }

    public function delete($id) {
        $rule = new Rules();
        $rule->modifiedDt = Helper::getcurrentExpressionDate();
        $rule->status = 'D';
        $this->tableGateway->update($rule->getArrayCopyForDB(), [Rules::PAY_ID => $id]);
    }

    public function fetchReferencingRules($payId = null): array {

        if ($payId == null) {
            $sql = "
                SELECT P.PAY_ID,
                  INITCAP(P.PAY_EDESC) AS PAY_EDESC,
                  INITCAP(P.PAY_LDESC) AS PAY_LDESC
                FROM HRIS_PAY_SETUP P";
        } else {
            $sql = "
                SELECT P.PAY_ID,
                  INITCAP(P.PAY_EDESC) AS PAY_EDESC,
                  INITCAP(P.PAY_LDESC) AS PAY_LDESC
                FROM HRIS_PAY_SETUP P,
                  (SELECT PRIORITY_INDEX FROM HRIS_PAY_SETUP WHERE PAY_ID=$payId
                  ) PS
                WHERE P.PRIORITY_INDEX < PS.PRIORITY_INDEX";
        }
        return $this->rawQuery($sql);
    }

    public function fetchSSRules(): array {
        $sql = "SELECT PAY_ID,'H_'||PAY_ID AS PAY_ID_COL,PAY_EDESC
                FROM HRIS_PAY_SETUP
                WHERE INCLUDE_IN_SALARY='Y'
                AND PAY_TYPE_FLAG     IN ('A','D')
                AND STATUS ='E'
                ORDER BY PRIORITY_INDEX";
        return $this->rawQuery($sql);
    }

}
