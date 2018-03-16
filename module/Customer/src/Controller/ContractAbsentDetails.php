<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Model\ContractAbsentDetailsModel;
use Customer\Model\Customer;
use Customer\Repository\ContractAbsentDetailsRepo;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class ContractAbsentDetails extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(ContractAbsentDetailsRepo::class);
        $this->initializeForm(\Customer\Form\ContractAbsentDetailsForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
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

    public function addAction() {

        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost();
            $attendanceDate = $request->getPost('attendanceDate');
            $customerId = $request->getPost('customerId');
            $contractId = $request->getPost('contractId');
            $locationId = $request->getPost('locationId');
            $employee = $request->getPost('employee');
            $empDesId = $request->getPost('empDesId');
            $empShiftId = $request->getPost('empShiftId');
            $subEmployee = $request->getPost('subEmployee');
            $subDesId = $request->getPost('subDesId');
            $postingType = $request->getPost('postingType');


            if ($employee) {
                $contractAbsentDetailsModule = new ContractAbsentDetailsModel();

                $contractAbsentDetailsModule->attendanceDate = Helper::getExpressionDate($attendanceDate);
                $contractAbsentDetailsModule->status = 'E';
                $contractAbsentDetailsModule->createdBy = $this->employeeId;
                $contractAbsentDetailsModule->customerId = $customerId;
                $contractAbsentDetailsModule->contractId = $contractId;
                $contractAbsentDetailsModule->employeeLocationId = $locationId;

                $i = 0;
                foreach ($employee as $employeeId) {
                    if ($employeeId > 0) {
                        $contractAbsentDetailsModule->id = (int) Helper::getMaxId($this->adapter, ContractAbsentDetailsModel::TABLE_NAME, ContractAbsentDetailsModel::ID) + 1;
                        $contractAbsentDetailsModule->employeeId = $employeeId;
                        $contractAbsentDetailsModule->employeeDesignationId = $empDesId[$i];
                        $contractAbsentDetailsModule->employeeShiftId = $empShiftId[$i];
                        $contractAbsentDetailsModule->subEmployeeId = $subEmployee[$i];
                        $contractAbsentDetailsModule->subDesignationId = $subDesId[$i];
                        $contractAbsentDetailsModule->postingType = $postingType[$i];
                        $this->repository->add($contractAbsentDetailsModule);
                    }
                    $i++;
                }
                $this->flashmessenger()->addMessage("substitute details Sucessfully Updated");
            }

            $this->redirect()->toRoute("contract-absent-details");
        }

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

    public function pullEmployeeDetailsAction() {
        try {
            $request = $this->getRequest();
            $employeeId = $request->getPost('employeeId');
            $contractId = $request->getPost('contractId');


            $retunrData = [];

            $employeeContractDetails = $this->repository->getAllEmployeeDetails($employeeId, $contractId);



            $retunrData['employeeContractDetails'] = $employeeContractDetails;

            return new JsonModel(['success' => true, 'data' => $retunrData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pullAllContractCustomerWiseAction() {
        try {
            $request = $this->getRequest();
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

    public function pullAllLocationContractWiseAction() {
        try {
            $request = $this->getRequest();
            $contractId = $request->getPost('contractId');
            if (!$contractId) {
                throw new Exception('no Contract ID passed');
            }

            $sql = "select location_id,location_name from HRIS_CUSTOMER_LOCATION where 
                location_id in (select distinct location_id from
                HRIS_CUST_CONTRACT_EMP
                where status='E'
                and  
            contract_id={$contractId})";
            $locationDetails = EntityHelper::rawQueryResult($this->adapter, $sql);
            $returnData = Helper::extractDbData($locationDetails);
            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {

            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pullEmployeeContractLocationAction() {
        try {
            $request = $this->getRequest();
            $contractId = $request->getPost('contractId');
            $locationId = $request->getPost('locationId');
            $attendanceDate = $request->getPost('attendanceDate');


            $sql = "select E.EMPLOYEE_ID,'('||E.EMPLOYEE_CODE||') '||E.FULL_NAME AS FULL_NAME from HRIS_CUST_CONTRACT_EMP CE
                join HRIS_EMPLOYEES E ON (E.EMPLOYEE_ID=CE.EMPLOYEE_ID)
                where CE.status='E'
                and CE.contract_id={$contractId}
                and CE.location_id={$locationId} AND 
                E.EMPLOYEE_ID NOT IN (SELECT EMPLOYEE_ID FROM  HRIS_CONTRACT_EMP_ABSENT_SUB where  STATUS='E' AND ATTENDANCE_DATE='{$attendanceDate}' AND CONTRACT_ID={$contractId} AND EMPLOYEE_LOCATION_ID={$locationId})";



            $employeeDetails = EntityHelper::rawQueryResult($this->adapter, $sql);
            $returnData = Helper::extractDbData($employeeDetails);

            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id == -1) {
            return $this->redirect()->toRoute('contract-absent-details');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage(" deleted successfully.");
        return $this->redirect()->toRoute("contract-absent-details");
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id == -1) {
            return $this->redirect()->toRoute('contract-absent-details');
        }

        $contractAbsentDetailModel = new ContractAbsentDetailsModel();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $contractAbsentDetailModel->exchangeArrayFromForm($this->form->getData());
                $contractAbsentDetailModel->modifiedDt = Helper::getcurrentExpressionDate();
                $contractAbsentDetailModel->modifiedBy = $this->employeeId;

                $this->repository->edit($contractAbsentDetailModel, $id);
                $this->flashmessenger()->addMessage("Successfully Updated!!!");
                return $this->redirect()->toRoute("contract-absent-details");
            }
        }


        $details = $this->repository->fetchById($id);
        $contractAbsentDetailModel->exchangeArrayFromDB($details);
        $this->form->bind($contractAbsentDetailModel);

        $employeeListSql = "select E.EMPLOYEE_ID,'('||E.EMPLOYEE_CODE||') '||E.FULL_NAME||'('||D.DESIGNATION_TITLE||')'  AS FULL_NAME 
            from  HRIS_EMPLOYEES E
            LEFT JOIN HRIS_DESIGNATIONS D ON (D.DESIGNATION_ID=E.DESIGNATION_ID)
            where E.status='E' and E.RESIGNED_FLAG='N'";


        $employeeDetails = EntityHelper::rawQueryResult($this->adapter, $employeeListSql);
        $employeeList = Helper::extractDbData($employeeDetails);



        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'form' => $this->form,
                    'employeeList' => $employeeList,
        ]);
    }

}
