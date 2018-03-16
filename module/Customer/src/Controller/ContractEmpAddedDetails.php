<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Model\Customer;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class ContractEmpAddedDetails extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
//        $this->initializeRepository(ContractAbsentDetailsRepo::class);
//        $this->initializeForm(\Customer\Form\ContractAbsentDetailsForm::class);
    }

    public function indexAction() {

        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl
        ]);
    }

    public function addAction() {
        $employeeListSql = "select EMPLOYEE_ID,'('||EMPLOYEE_CODE||') '||FULL_NAME AS FULL_NAME ,retired_flag
            from  HRIS_EMPLOYEES where status='E' and RESIGNED_FLAG='N'";


        $employeeDetails = EntityHelper::rawQueryResult($this->adapter, $employeeListSql);
        $employeeList = Helper::extractDbData($employeeDetails);


        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    'customerList' => EntityHelper::getTableList($this->adapter, Customer::TABLE_NAME, [Customer::CUSTOMER_ID, Customer::CUSTOMER_ENAME], [Customer::STATUS => "E"]),
                    'employeeList' => $employeeList
        ]);
    }

}
