<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Model\CustContractEmp;
use Customer\Model\Customer;
use Customer\Repository\ContractAttendanceRepo;
use Customer\Repository\CustContractEmpRepo;
use Customer\Repository\CustomerContractRepo;
use Customer\Repository\CustomerLocationRepo;
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
//        $request = $this->getRequest();
//        if ($request->isPost()) {
//            try {
//                $customerRepo = new CustomerContractRepo($this->adapter);
//                $result = $customerRepo->fetchAll();
//                $list = Helper::extractDbData($result);
//                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
//            } catch (Exception $e) {
//                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
//            }
//        }


        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    'customerList' => EntityHelper::getTableList($this->adapter, Customer::TABLE_NAME, [Customer::CUSTOMER_ID, Customer::CUSTOMER_ENAME], [Customer::STATUS => "E"]),
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E"]),
                    'locationList' => $locationList
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

        $customerId = $customerContractDetails['CUSTOMER_ID'];

        $customerLocationRepo = new CustomerLocationRepo($this->adapter);
        $locationList = $customerLocationRepo->fetchAllLocationByCustomer($customerId);

        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E"]),
                    'customerContractDetails' => $customerContractDetails,
//                    'contractEmpDetails' => $custEmployeeDetails,
                    'customerId' => $customerId,
                    'locationList' => $locationList
        ]);
    }

    public function employeeAssignAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
//            $customerId = $request->getPost('customerId');
            $contractId = $request->getPost('contractId');
            $employeedesignation = $request->getPost('designation');
            $employees = $request->getPost('employee');
            $employeeLocation = $request->getPost('location');
            $employeeStartTime = $request->getPost('employeeStartTime');
            $employeeEndTime = $request->getPost('employeeEndTime');


//            echo '<pre>';
//            print_r($postData);
//
//            die();

            if ($employees) {
                $i = 0;
                $custEmployeeModel = new CustContractEmp();
                $custEmployeeModel->contractId = $contractId;

                $custEmployeeModel->status = 'D';
                $custEmployeeModel->modifiedDt = Helper::getcurrentExpressionDate();
                $custEmployeeModel->modifiedBy = $this->employeeId;



//                echo'<Pre>';
//                print_r($custEmployeeModel);
//                die();
                //to delete old assigned
                $this->repository->edit($custEmployeeModel, $contractId);


                $custEmployeeRepo = new CustContractEmpRepo($this->adapter);
                $custEmployeeModel->customerId = 1;
                $custEmployeeModel->lastAssignedDate = Helper::getCurrentDate();

                foreach ($employees as $employeeDetails) {
                    if ($employeeDetails > 0) {
                        $custEmployeeModel->employeeId = $employeeDetails;
                        $custEmployeeModel->designationId = $employeedesignation[$i];
                        $custEmployeeModel->locationId = $employeeLocation[$i];
                        $custEmployeeModel->startTime = Helper::getExpressionTime($employeeStartTime[$i]);
                        $custEmployeeModel->endTime = Helper::getExpressionTime($employeeEndTime[$i]);
                        $custEmployeeModel->status = 'E';
                        $custEmployeeModel->modifiedDt = NULL;
                        $custEmployeeModel->modifiedDt = NULL;

                        $custEmployeeRepo->add($custEmployeeModel);
                    }
                    $i++;
                }
            }


            $this->flashmessenger()->addMessage("Contract Employee updated successfully.");
            $this->redirect()->toRoute("contract-employees", ["action" => "index"]);
//            return $this->redirect()->toRoute("contract-employees", ["action" => "view", "id" => $id]);
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
            $employeeDetails = $this->repository->getAllMonthWiseEmployees($id, $monthId);
            return new JsonModel(['success' => true, 'data' => $employeeDetails, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function fetchAllContractCustomerWiseAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();
            $cutomerId = $request->getPost('customerId');
            if (!$cutomerId) {
                throw new Exception('no CustomerId passed');
            }
            $contractDetails = EntityHelper::getTableList($this->adapter, \Customer\Model\CustomerContract::TABLE_NAME, [\Customer\Model\CustomerContract::CONTRACT_ID, \Customer\Model\CustomerContract::CONTRACT_NAME], [\Customer\Model\CustomerContract::CUSTOMER_ID => $cutomerId, \Customer\Model\CustomerContract::STATUS => "E"]);
            return new JsonModel(['success' => true, 'data' => $contractDetails, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function fetchContractDetailsAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();
            $contractId = $request->getPost('contractId');
            if (!$contractId) {
                throw new Exception('no ContractId passed');
            }
            $contractDetailRepo = new \Customer\Repository\CustomerContractDetailRepo($this->adapter);
            $contractDetails = $contractDetailRepo->fetchAllContractDetailByContractId($contractId);
            $locationList = [];


            $customerId = $contractDetails[0]['CUSTOMER_ID'];
            if ($customerId > 0) {
                $customerLocationRepo = new CustomerLocationRepo($this->adapter);
                $locationList = $customerLocationRepo->fetchAllLocationByCustomer($customerId);
            }
            $returnData = [];
            $returnData['locationList'] = $locationList;
            $returnData['contractDetails'] = $contractDetails;
            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function fetchContractDesginationWiseEmployeeAssignAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();

            $contractId = $request->getPost('contractId');
            $designationId = $request->getPost('designationId');

            $employeeData = $this->repository->getEmployeeAssignedDesignationWise($contractId, $designationId);

            return new JsonModel(['success' => true, 'data' => $employeeData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
