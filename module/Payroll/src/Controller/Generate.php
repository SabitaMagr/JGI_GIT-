<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/20/16
 * Time: 4:32 PM
 */

namespace Payroll\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Payroll\Model\Rules;
use Setup\Model\Branch;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Mvc\Controller\AbstractActionController;

class Generate extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function indexAction() {
        $employeeList = EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E'], ' ');
        $rules = EntityHelper::getTableKVListWithSortOption($this->adapter, Rules::TABLE_NAME, Rules::PAY_ID, [Rules::PAY_EDESC], [Rules::STATUS => 'E'], Rules::PRIORITY_INDEX, Select::ORDER_ASCENDING, null);
        $branches = EntityHelper::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E']);
        $fiscalYears = EntityHelper::getTableKVList($this->adapter, FiscalYear::TABLE_NAME, FiscalYear::FISCAL_YEAR_ID, [FiscalYear::START_DATE, FiscalYear::END_DATE], [FiscalYear::STATUS => 'E'], "-");

        return Helper::addFlashMessagesToArray($this, [
                    'employeeList' => $employeeList,
                    'rules' => $rules,
                    'branches' => $branches,
                    'fiscalYears' => $fiscalYears
        ]);
    }

}
