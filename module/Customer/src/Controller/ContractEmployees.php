<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Model\CustContractEmp;
use Customer\Model\Customer;
use Customer\Model\DutyTypeModel;
use Customer\Repository\ContractAttendanceRepo;
use Customer\Repository\CustContractEmpRepo;
use Customer\Repository\CustomerContractRepo;
use Customer\Repository\CustomerLocationRepo;
use Exception;
use Setup\Model\Designation;
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

        $employeeListSql = "select E.EMPLOYEE_ID,'('||E.EMPLOYEE_CODE||') '||E.FULL_NAME||' ('||D.DESIGNATION_TITLE||')'  AS FULL_NAME 
            from  HRIS_EMPLOYEES E
            LEFT JOIN HRIS_DESIGNATIONS D ON (D.DESIGNATION_ID=E.DESIGNATION_ID)
            where E.status='E' and E.RESIGNED_FLAG='N'";


        $employeeDetails = EntityHelper::rawQueryResult($this->adapter, $employeeListSql);
        $employeeList = Helper::extractDbData($employeeDetails);


        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    'customerList' => EntityHelper::getTableList($this->adapter, Customer::TABLE_NAME, [Customer::CUSTOMER_ID, Customer::CUSTOMER_ENAME], [Customer::STATUS => "E"]),
                    'designationList' => EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, [Designation::DESIGNATION_ID, Designation::DESIGNATION_TITLE], [Designation::STATUS => "E"]),
                    'dutyTypeList' => EntityHelper::getTableList($this->adapter, DutyTypeModel::TABLE_NAME, [DutyTypeModel::DUTY_TYPE_ID, DutyTypeModel::DUTY_TYPE_NAME], [DutyTypeModel::STATUS => "E"]),
                    'employeeList' => $employeeList
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
            $customerId = $request->getPost('customerId');
            $contractId = $request->getPost('contractId');
            $employeedesignation = $request->getPost('designation');
            $employees = $request->getPost('employee');
            $employeeLocation = $request->getPost('location');
            $employeeStartTime = $request->getPost('employeeStartTime');
            $employeeEndTime = $request->getPost('employeeEndTime');



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
                $custEmployeeModel->customerId = $customerId;
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

    public function fetchContractWiseEmployeeAssignAction() {
        try {
            $request = $this->getRequest();
            $contractId = $request->getPost('contractId');
            $customerId = $request->getPost('customerId');

            $customerLocationRepo = new CustomerLocationRepo($this->adapter);
            $locationList = $customerLocationRepo->fetchAllLocationByCustomer($customerId);

            $employeeData = $this->repository->getEmployeeAssignedContractWise($contractId);


            $returnData['locationList'] = $locationList;
            $returnData['empDetails'] = $employeeData;
            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function fetchContractEmpLocDesWiseAction() {
        try {
            $request = $this->getRequest();

            $contractId = $request->getPost('contractId');
            $employeeId = $request->getPost('employeeId');
            $locationId = $request->getPost('locationId');
            $designationId = $request->getPost('designationId');


            $returnData = $this->repository->getContractEmpLocDesWise($contractId, $employeeId, $locationId, $designationId);

            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function addContractEmpAssignAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();

            $customer = $request->getPost('customer');
            $contract = $request->getPost('contract');
            $designation = $request->getPost('designation');
            $employee = $request->getPost('employee');
            $location = $request->getPost('location');
            $startDate = $request->getPost('startDate');
            $endDate = $request->getPost('endDate');
            $startTime = $request->getPost('startTime');
            $endTime = $request->getPost('endTime');
            $dutyType = $request->getPost('dutyType');

//            echo $dutyType

            $custEmployeeModel = new CustContractEmp();

            $custEmployeeModel->id = (int) Helper::getMaxId($this->adapter, CustContractEmp::TABLE_NAME, CustContractEmp::ID) + 1;

            $custEmployeeModel->customerId = $customer;
            $custEmployeeModel->contractId = $contract;
            $custEmployeeModel->employeeId = $employee;
            $custEmployeeModel->designationId = $designation;
            $custEmployeeModel->locationId = $location;
            $custEmployeeModel->startDate = Helper::getExpressionDate($startDate);
            $custEmployeeModel->endDate = Helper::getExpressionDate($endDate);
            $custEmployeeModel->startTime = Helper::getExpressionTime($startTime);
            $custEmployeeModel->endTime = Helper::getExpressionTime($endTime);
            $custEmployeeModel->status = 'E';
            $custEmployeeModel->createdBy = $this->employeeId;
            $custEmployeeModel->dutyTypeId = $dutyType;




            $this->repository->add($custEmployeeModel);
            $employeeData = $this->repository->getEmployeeAssignedContractWise($contract);
            return new JsonModel(['success' => true, 'data' => $employeeData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function deleteAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();



            $id = $request->getPost('id');
            $contractId = $request->getPost('contractId');

            $this->repository->delete($id);
            $employeeData = $this->repository->getEmployeeAssignedContractWise($contractId);

            return new JsonModel(['success' => true, 'data' => $employeeData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function updateAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();

            $id = $request->getPost('id');
            $contract = $request->getPost('contract');
            $designation = $request->getPost('designation');
            $employee = $request->getPost('employee');
            $location = $request->getPost('location');
            $startDate = $request->getPost('startDate');
            $endDate = $request->getPost('endDate');
            $startTime = $request->getPost('startTime');
            $endTime = $request->getPost('endTime');
            $dutyType = $request->getPost('dutyType');


            $custEmployeeModel = new CustContractEmp();


            $custEmployeeModel->employeeId = $employee;
            $custEmployeeModel->designationId = $designation;
            $custEmployeeModel->locationId = $location;
            $custEmployeeModel->startDate = Helper::getExpressionDate($startDate);
            $custEmployeeModel->endDate = Helper::getExpressionDate($endDate);
            $custEmployeeModel->startTime = Helper::getExpressionTime($startTime);
            $custEmployeeModel->endTime = Helper::getExpressionTime($endTime);
            $custEmployeeModel->dutyTypeId = $dutyType;
            $custEmployeeModel->modifiedBy = $this->employeeId;
            $custEmployeeModel->modifiedDt = Helper::getcurrentExpressionDate();

            $this->repository->edit($custEmployeeModel, $id);
            $employeeData = $this->repository->getEmployeeAssignedContractWise($contract);

            return new JsonModel(['success' => true, 'data' => $employeeData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
