<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Exception;
use Payroll\Form\Rules as RuleForm;
use Payroll\Model\FlatValue;
use Payroll\Model\MonthlyValue;
use Payroll\Model\PayEmployeeSetup;
use Payroll\Model\Rules as RulesModel;
use Payroll\Repository\PayEmployeeRepo;
use Payroll\Repository\RulesRepository;
use Setup\Model\Gender;
use Setup\Model\ServiceType;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class Rules extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(RulesRepository::class);
        $this->initializeForm(RuleForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
//                $data = $request->getPost();
                $rawList = $this->repository->fetchAll();
                $list = Helper::extractDbData($rawList);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return $this->stickFlashMessagesTo(['acl' => $this->acl]);
    }

    public function addAction() {
        
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $monthlyValues = EntityHelper::getTableKVListWithSortOption($this->adapter, MonthlyValue::TABLE_NAME, MonthlyValue::MTH_ID, [MonthlyValue::MTH_EDESC], [MonthlyValue::STATUS => 'E'], null, null, null, false, true);
        $flatValues = EntityHelper::getTableKVListWithSortOption($this->adapter, FlatValue::TABLE_NAME, FlatValue::FLAT_ID, [FlatValue::FLAT_EDESC], [MonthlyValue::STATUS => 'E'], null, null, null, false, true);
        $fiscalYears = EntityHelper::getTableKVListWithSortOption($this->adapter, FiscalYear::TABLE_NAME, FiscalYear::FISCAL_YEAR_ID, [FiscalYear::START_DATE, FiscalYear::END_DATE], [FiscalYear::STATUS => 'E'], null, null, "-", false, true);
        $genders = EntityHelper::getTableKVListWithSortOption($this->adapter, Gender::TABLE_NAME, Gender::GENDER_ID, [Gender::GENDER_NAME], [Gender::STATUS => 'E'], null, null, null, false, true);
        $serviceTypes = EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => 'E'], null, null, null, false, true);

        return Helper::addFlashMessagesToArray($this, [
                    'monthlyValues' => $monthlyValues,
                    'flatValues' => $flatValues,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    "variables" => PayrollGenerator::VARIABLES,
                    "systemRules" => PayrollGenerator::SYSTEM_RULE,
                    'id' => $id,
                    'fiscalYears' => $fiscalYears,
                    'genders' => $genders,
                    'serviceTypes' => $serviceTypes]
        );
    }

    public function pullReferencedRulesAction() {
        $request = $this->getRequest();
        $data = [];
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $payId = $postedData['payId'];
            $refRules = $this->repository->fetchReferencingRules($payId);
            $data = Helper::extractDbData($refRules);
        } else {
            $data = ['success' => FALSE, 'message' => 'Request should be of post method'];
        }

        $view = new ViewModel(['data' => $data]);
        $view->setTerminal(true);
        $view->setTemplate('layout/json');
        return $view;
    }

    public function getRuleEmployeeAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $postedData = $request->getPost();
            $payId = $postedData['payId'];
            $payEmployeeRepo = new PayEmployeeRepo($this->adapter);
            $rawResult = $payEmployeeRepo->fetchByPayId($payId);
            $result = [];
            foreach ($rawResult as $item) {
                array_push($result, $item['EMPLOYEE_ID']);
            }

            return new CustomViewModel(['success' => true, 'data' => $result, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function putRuleEmployeeAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $postedData = $request->getPost();
            $payId = $postedData['payId'];
            $employees = $postedData['employees'];
            $payEmployeeRepo = new PayEmployeeRepo($this->adapter);
            $payEmployeeRepo->deleteByPayId($payId);
            foreach ($employees as $employeeId) {
                $payEmployee = new PayEmployeeSetup();
                $payEmployee->payId = $payId;
                $payEmployee->employeeId = $employeeId;
                $payEmployeeRepo->add($payEmployee);
            }

            return new CustomViewModel(['success' => true, 'data' => $employees, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pushRuleAction() {
        try {
            $return = [];
            $request = $this->getRequest();
            $data = $request->getPost();

            $repository = new RulesRepository($this->adapter);
            $auth = new AuthenticationService();

            $rulesValue = new RulesModel();
            $rulesValue->exchangeArrayFromForm((array) $data);
            if ($rulesValue->payId != NULL) {
                $payId = $rulesValue->payId;
                unset($rulesValue->payId);
                unset($rulesValue->createdDt);
                unset($rulesValue->createdBy);
                unset($rulesValue->status);
                unset($rulesValue->refRuleFlag);

                $rulesValue->modifiedDt = Helper::getcurrentExpressionDate();
                $rulesValue->modifiedBy = $auth->getStorage()->read()['user_id'];
                $repository->edit($rulesValue, $payId);
                $return = ["success" => true, "message" => "Rule successfully edited"];
            } else {
                $rulesValue->payId = ((int) Helper::getMaxId($this->adapter, RulesModel::TABLE_NAME, RulesModel::PAY_ID)) + 1;
                $rulesValue->createdDt = Helper::getcurrentExpressionDate();
                $rulesValue->status = 'E';
                $rulesValue->refRuleFlag = 'N';

                $rulesValue->createdBy = $auth->getStorage()->read()['user_id'];
                $repository->add($rulesValue);
                $return = ["success" => true, "message" => "Rule successfully added", "data" => ["payId" => $rulesValue->payId]];
            }

            return new JsonModel($return);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function pullRuleAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $repository = new RulesRepository($this->adapter);

            return new JsonModel(['success' => true, 'data' => ["rule" => $repository->fetchById($data['ruleId'])], 'message' => "Rule successfully added"]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
