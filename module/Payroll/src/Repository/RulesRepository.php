<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 3:01 PM
 */

namespace Payroll\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\Rules;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\I18n\Translator\Plural\Rule;

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
        return $this->gateway->select([Rules::STATUS => 'E']);
    }

    public function fetchById($id) {
        return $this->gateway->select([Rules::STATUS => 'E', Rules::PAY_ID => $id])->current();
    }

    public function delete($id) {
        $rule = new Rules();
        $rule->modifiedDt = Helper::getcurrentExpressionDate();
        $rule->status = 'D';
        $this->gateway->update($rule->getArrayCopyForDB(), [Rules::PAY_ID => $id]);
    }

    public function fetchReferencingRules($payId) {
        $sql = "SELECT P.PAY_ID,
  P.PAY_EDESC,
  P.PAY_LDESC
FROM HR_PAY_SETUP P,
  (SELECT PRIORITY_INDEX FROM HR_PAY_SETUP WHERE PAY_ID=$payId
  ) PS
WHERE P.PRIORITY_INDEX < PS.PRIORITY_INDEX";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
