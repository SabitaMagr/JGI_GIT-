<?php

namespace Advance\Controller;

use Advance\Form\AdvanceRequestForm;
use Advance\model\AdvanceSetupModel;
use Advance\Repository\AdvanceRequestSelfRepository;
use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class AdvanceRequestSelf extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(AdvanceRequestSelfRepository::class);
        $this->initializeForm(AdvanceRequestForm::class);
    }

    public function indexAction() {


        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeId' => $this->employeeId,
                    'advance' => EntityHelper::getTableList($this->adapter, AdvanceSetupModel::TABLE_NAME, ['*'], [AdvanceSetupModel::STATUS => 'E']),
//                    'advance' => EntityHelper::getTableKVListWithSortOption($this->adapter, AdvanceSetupModel::TABLE_NAME, AdvanceSetupModel::ADVANCE_ID, [AdvanceSetupModel::ADVANCE_ENAME], [AdvanceSetupModel::STATUS=>'E'], AdvanceSetupModel::ADVANCE_ENAME, "ASC", " ", false,true),
                    'customRenderer' => Helper::renderCustomView(),
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"])
        ]);
    }

    public function viewAction() {
        
    }

}
