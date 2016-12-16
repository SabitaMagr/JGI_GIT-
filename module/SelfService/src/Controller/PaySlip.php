<?php

namespace SelfService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Payroll\Model\Rules;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Mvc\Controller\AbstractActionController;

class PaySlip extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function indexAction() {
        $rules = EntityHelper::getTableKVListWithSortOption($this->adapter, Rules::TABLE_NAME, Rules::PAY_ID, [Rules::PAY_EDESC], [Rules::STATUS => 'E'], Rules::PRIORITY_INDEX, Select::ORDER_ASCENDING, null);
        return Helper::addFlashMessagesToArray($this, [
                    'rules' => $rules,
        ]);
    }

}
