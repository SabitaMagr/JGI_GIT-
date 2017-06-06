<?php

namespace Payroll\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\Rules;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class RulesRepository implements RepositoryInterface {

    private $adapter;
    private $gateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->gateway = new TableGateway(Rules::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        return $this->gateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id) {
        $this->gateway->update($model->getArrayCopyForDB(), [Rules::PAY_ID => $id]);
    }

    public function fetchAll() {
        return $this->gateway->select(function(Select $select) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Rules::class, [Rules::PAY_EDESC, Rules::PAY_LDESC]), false);
                    $select->where([Rules::STATUS => 'E']);
                    $select->order([Rules::PRIORITY_INDEX => Select::ORDER_ASCENDING]);
                });
    }

    public function fetchById($id) {
        return $this->gateway->select(function(Select $select) use($id) {
                    $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Rules::class, [Rules::PAY_EDESC, Rules::PAY_LDESC]), false);
                    $select->where([Rules::STATUS => 'E', Rules::PAY_ID => $id]);
                })->current();
    }

    public function delete($id) {
        $rule = new Rules();
        $rule->modifiedDt = Helper::getcurrentExpressionDate();
        $rule->status = 'D';
        $this->gateway->update($rule->getArrayCopyForDB(), [Rules::PAY_ID => $id]);
    }

    public function fetchReferencingRules($payId) {
        $sql = "
                SELECT P.PAY_ID,
                  INITCAP(P.PAY_EDESC) AS PAY_EDESC,
                  INITCAP(P.PAY_LDESC) AS PAY_LDESC
                FROM HRIS_PAY_SETUP P,
                  (SELECT PRIORITY_INDEX FROM HRIS_PAY_SETUP WHERE PAY_ID=$payId
                  ) PS
                WHERE P.PRIORITY_INDEX < PS.PRIORITY_INDEX";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
