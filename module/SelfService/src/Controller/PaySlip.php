<?php

namespace SelfService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Payroll\Controller\PayrollGenerator;
use Payroll\Controller\SalarySheet;
use Payroll\Controller\VariableProcessor;
use Payroll\Repository\RulesRepository;
use Payroll\Repository\SalarySheetRepo;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class PaySlip extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function indexAction() {
        $rulesRepo = new RulesRepository($this->adapter);
        $rulesRaw = $rulesRepo->fetchAll();
        $rules = Helper::extractDbData($rulesRaw);


        $auth = new AuthenticationService();
        $employeeId = $auth->getStorage()->read()['employee_id'];
        return Helper::addFlashMessagesToArray($this, [
                    'rules' => $rules,
                    'employeeId' => $employeeId
        ]);
    }

    public function pullPayRollGeneratedMonthsAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $employeeId = null;
            $joinDate = null;
            if (isset($data['employeeId'])) {
                $employeeId = $data['employeeId'];
            }
            if ($employeeId != null) {
                $result = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, null, [HrEmployees::JOIN_DATE], [HrEmployees::EMPLOYEE_ID => $employeeId], null, null);
                if (sizeof($result) > 0) {
                    $joinDate = $result[0];
                }
            }
            $salarySheetRepo = new SalarySheetRepo($this->adapter);
            $generatedSalarySheets = Helper::extractDbData($salarySheetRepo->joinWithMonth(null, $joinDate));

            return new JsonModel(['success' => true, 'data' => $generatedSalarySheets, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function fetchEmployeePaySlipAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $monthId = $data['month'];
            $auth = new AuthenticationService();
            $employeeId = $auth->getStorage()->read()['employee_id'];

            $salarySheetController = new SalarySheet($this->adapter);
            if ($salarySheetController->checkIfGenerated($monthId)) {
                $employeeList['EMPLOYEE_ID'] = $employeeId;
                $results = $salarySheetController->viewSalarySheet($monthId, $employeeList);
                $results = $results[$employeeId];
            }

            $employeeRepo = new EmployeeRepository($this->adapter);
            $employee = $employeeRepo->fetchForProfileById($employeeId);
            $results['employeeDetail'] = $employee;

            $variableProcessor = new VariableProcessor($this->adapter, $employeeId, $monthId);
            $absentDays = $variableProcessor->processVariable(PayrollGenerator::VARIABLES[2]);
            $results["absentDays"] = $absentDays;

            $presentDays = $variableProcessor->processVariable(PayrollGenerator::VARIABLES[3]);
            $results["presentDays"] = $presentDays;

            return new JsonModel(['success' => true, 'data' => $results, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
