<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Exception;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SalarySheetRepo;
use Payroll\Repository\TaxSheetRepo;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class TaxSheetController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(TaxSheetRepo::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $postedData = $request->getPost();
                $data = $this->repository->fetchTaxSheetPivoted($postedData);
                return new JsonModel(['success' => true, 'data' => $data, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $ruleRepo = new RulesRepository($this->adapter);
        $salarySheetRepo = new SalarySheetRepo($this->adapter);
        $data['ruleList'] = iterator_to_array($ruleRepo->fetchAll(), false);
        $data['salarySheetList'] = iterator_to_array($salarySheetRepo->fetchAll(), false);
        $data['acl'] = $this->acl;
		$data['employeeDetail'] = $this->storageData['employee_detail'];
		//'employeeDetail' => $this->storageData['employee_detail'],
        return $data;
    }

    public function taxslipAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $postedData = $request->getPost();
                $data = $this->repository->fetchEmployeeTaxSlip($postedData['monthId'], $postedData['employeeId']);
                return new JsonModel(['success' => true, 'data' => $data, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }

    public function getSearchDataAction(){
        $acl =  $this->acl;
        if($acl['CONTROL'][0]=='C'){
            $searchValues = EntityHelper::getSearchDataCompanyWise($this->adapter, true, $acl['CONTROL_VALUES'][0]['VAL']);
        }else{
            $searchValues = EntityHelper::getSearchDataCompanyWise($this->adapter, false, null);
        }
      return new JsonModel(['success' => true, 'data' => $searchValues, 'error' => '']);
    }

}
