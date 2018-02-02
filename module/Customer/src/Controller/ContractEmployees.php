<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Model\CustContractEmp;
use Customer\Repository\ContractAttendanceRepo;
use Customer\Repository\CustContractEmpRepo;
use Customer\Repository\CustomerContractRepo;
use Exception;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class ContractEmployees extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(CustContractEmpRepo::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $customerRepo = new CustomerContractRepo($this->adapter);
                $result = $customerRepo->fetchAll();
                $list = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl
        ]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("contract-attendance");
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $contractAttendnceRepo = new ContractAttendanceRepo($this->adapter);
                $result = $contractAttendnceRepo->fetchById($id);
                $list = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        $customerContractRepo = new CustomerContractRepo($this->adapter);
        $customerContractDetails = $customerContractRepo->fetchById($id);

        $custEmployeeRepo = new CustContractEmpRepo($this->adapter);
        $custEmployeeDetails = $custEmployeeRepo->fetchById($id);


        $contractStartDate = $customerContractDetails['START_DATE'];
        $contractEndDate = $customerContractDetails['END_DATE'];

        $monthDetails = $this->repository->getAllMonthBetweenTwoDates($contractStartDate, $contractEndDate);

        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"]),
                    'customerContractDetails' => $customerContractDetails,
                    'contractEmpDetails' => $custEmployeeDetails,
                    'monthDetails' => $monthDetails
        ]);
    }

    public function employeeAssignAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("contract-employees");
        }


        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();

            $monthId = $request->getPost('monthId');

//            echo '<Pre>';
//            print_r($postData);
//            die();


            $employees = $request->getPost('employee');

            $custEmployeeRepo = new CustContractEmpRepo($this->adapter);
            $custEmployeeRepo->deleteContractEmpMonthly($id, $monthId);

            if ($employees) {
                $totalWorkingHr = $request->getPost('totalWorkingHr');
                $employeeStartTime = $request->getPost('employeeStartTime');
                $employeeEndTime = $request->getPost('employeeEndTime');
                $i = 0;
                $custEmployeeModel = new CustContractEmp();
                $custEmployeeModel->contractId = $id;
                $custEmployeeModel->assignedDate = Helper::getCurrentDate();
                $custEmployeeModel->monthCodeId = $monthId;

                echo '<pre>';

                foreach ($employees as $employeeDetails) {
                    if ($employeeDetails > 0) {
                        $custEmployeeModel->employeeId = $employeeDetails;
                        $custEmployeeModel->workingHour = Helper::hoursToMinutes($totalWorkingHr[$i]);
                        $custEmployeeModel->startTime = Helper::getExpressionTime($employeeStartTime[$i]);
                        $custEmployeeModel->endTime = Helper::getExpressionTime($employeeEndTime[$i]);
                        $custEmployeeRepo->add($custEmployeeModel);
                    }
                    $i++;
                }
            }


            EntityHelper::rawQueryResult($this->adapter, "BEGIN
                    HRIS_ATTD_BETWEEN_DATES({$id},{$monthId});
                        END;");


            $this->flashmessenger()->addMessage("Contract Employee updated successfully.");
            return $this->redirect()->toRoute("contract-employees", ["action" => "view", "id" => $id]);
        }
    }

    public function monthWiseEmployeeListAction() {

        try {
            $id = (int) $this->params()->fromRoute("id");
            if ($id === 0) {
                throw new Exception('id is undefined');
            }
            $request = $this->getRequest();
            $postData = $request->getPost();
            $monthId = $request->getPost('monthId');
            $employeeDetails = $this->repository->getAllMonthWiseEmployees($monthId);
            return new JsonModel(['success' => true, 'data' => $employeeDetails, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
