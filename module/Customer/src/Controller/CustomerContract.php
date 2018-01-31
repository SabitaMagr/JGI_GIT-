<?php

namespace Customer\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Form\CustomerContractFrom;
use Customer\Model\Customer;
use Customer\Model\CustomerContract as CustomerContractModel;
use Customer\Repository\CustomerContractRepo;
use Exception;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CustomerContract extends AbstractActionController {

    private $form;
    private $repository;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->repository = new CustomerContractRepo($adapter);
        $this->adapter = $adapter;
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
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

    private function getForm() {
        if (!$this->form) {
            $form = new CustomerContractFrom();
            $builder = new AnnotationBuilder();
            $this->form = $builder->createForm($form);
        }

        return $this->form;
    }

    public function addAction() {
        $request = $this->getRequest();
        $form = $this->getForm();
        $customerIdElement = $form->get('customerId');
        $customerIdElement->setValueOptions(EntityHelper::getTableKVList($this->adapter, Customer::TABLE_NAME, Customer::CUSTOMER_ID, [Customer::CUSTOMER_ENAME], [Customer::STATUS => EntityHelper::STATUS_ENABLED], null, true));
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {


                $customerContract = new CustomerContractModel();
                $customerContract->exchangeArrayFromForm($form->getData());
                $customerContract->contractId = ((int) Helper::getMaxId($this->adapter, CustomerContractModel::TABLE_NAME, CustomerContractModel::CONTRACT_ID)) + 1;
                $customerContract->inTime = Helper::getExpressionTime($customerContract->inTime);
                $customerContract->outTime = Helper::getExpressionTime($customerContract->outTime);
                $customerContract->workingHours = Helper::hoursToMinutes($customerContract->workingHours);
                $customerContract->createdBy = $this->employeeId;

                $this->repository->add($customerContract);





                //if the working cycle is weekdays
                if ($customerContract->workingCycle == 'W') {
                    $custContractWeekdaysModel = new \Customer\Model\CustContractWeekdays();
                    $custContractWeekdaysRepo = new \Customer\Repository\CustContractWeekdaysRepo($this->adapter);
                    $custContractWeekdaysModel->contractId = $customerContract->contractId;

                    $weekArr = array('SUN' => 1, 'MON' => 2, 'TUE' => 3, 'WED' => 4, 'THU' => 5, 'FRI' => 6, 'SAT' => 7);
                    foreach ($weekArr as $key => $value) {
                        if ($request->getPost($key) == 'YES') {
                            $custContractWeekdaysModel->weekday = $value;
                            $custContractWeekdaysRepo->add($custContractWeekdaysModel);
                        }
                    }
                }

                //if the working cycle is ramdom dates
                if ($customerContract->workingCycle == 'R') {
                    $contractDates = $request->getPost('contractDates');
                    $custContractDatesModel = new \Customer\Model\CustContractDates();
                    $custContractDatesRepo = new \Customer\Repository\CustContractDatesRepo($this->adapter);
                    $custContractDatesModel->contractId = $customerContract->contractId;

                    foreach ($contractDates as $dates) {
                        if ($dates != null) {
                            echo $dates;
                            $custContractDatesModel->manualDate = Helper::getExpressionDate($dates);
                            $custContractDatesRepo->add($custContractDatesModel);
                        }
                    }
                }

//                EntityHelper::rawQueryResult($this->adapter, "BEGIN
//                    HRIS_ATTD_BETWEEN_DATES({$customerContract->contractId});
//                        END;");

                $this->flashmessenger()->addMessage("Customer Contract added successfully.");
                return $this->redirect()->toRoute("customer-contract");
            }
        }
        return new ViewModel([
            'form' => $form,
            'customRenderer' => Helper::renderCustomView(),
            'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"])
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("customer-contract");
        }
        $form = $this->getForm();

        $custEmployeeRepo = new \Customer\Repository\CustContractEmpRepo($this->adapter);
        $custEmployeeDetails = $custEmployeeRepo->fetchById($id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $customerContract = new CustomerContractModel();
                $customerContract->exchangeArrayFromForm($form->getData());

                $customerContract->inTime = Helper::getExpressionTime($customerContract->inTime);
                $customerContract->outTime = Helper::getExpressionTime($customerContract->outTime);
                $customerContract->workingHours = Helper::hoursToMinutes($customerContract->workingHours);
                $customerContract->modifiedBy = $this->employeeId;
                $customerContract->modifiedDt = Helper::getCurrentDate();



                $this->repository->edit($customerContract, $id);

                $custContractWeekdaysRepo = new \Customer\Repository\CustContractWeekdaysRepo($this->adapter);
                $custContractDatesRepo = new \Customer\Repository\CustContractDatesRepo($this->adapter);


                $custContractWeekdaysRepo->delete($id);
                $custContractDatesRepo->delete($id);





                //if the working cycle is weekdays
                if ($customerContract->workingCycle == 'W') {
                    $custContractWeekdaysModel = new \Customer\Model\CustContractWeekdays();
                    $custContractWeekdaysModel->contractId = $id;

                    $weekArr = array('SUN' => 1, 'MON' => 2, 'TUE' => 3, 'WED' => 4, 'THU' => 5, 'FRI' => 6, 'SAT' => 7);
                    foreach ($weekArr as $key => $value) {
                        if ($request->getPost($key) == 'YES') {
                            $custContractWeekdaysModel->weekday = $value;
                            $custContractWeekdaysRepo->add($custContractWeekdaysModel);
                        }
                    }
                }

                //if the working cycle is ramdom dates
                if ($customerContract->workingCycle == 'R') {
                    $contractDates = $request->getPost('contractDates');
                    print_r($contractDates);
                    $custContractDatesModel = new \Customer\Model\CustContractDates();
                    $custContractDatesModel->contractId = $id;

                    foreach ($contractDates as $dates) {
                        if ($dates != null) {
                            echo $dates;
                            $custContractDatesModel->manualDate = Helper::getExpressionDate($dates);
                            $custContractDatesRepo->add($custContractDatesModel);
                        }
                    }
                }
                
//                EntityHelper::rawQueryResult($this->adapter, "BEGIN
//                    HRIS_ATTD_BETWEEN_DATES({$id});
//                        END;");





                $this->flashmessenger()->addMessage("Customer Contract updated successfully.");
                return $this->redirect()->toRoute("customer-contract");
            }
        }




        $customerIdElement = $form->get('customerId');
        $customerIdElement->setValueOptions(EntityHelper::getTableKVList($this->adapter, Customer::TABLE_NAME, Customer::CUSTOMER_ID, [Customer::CUSTOMER_ENAME], [Customer::STATUS => EntityHelper::STATUS_ENABLED], null, true));
        $customerContract = new CustomerContractModel();
        $detail = $this->repository->fetchById($id)->getArrayCopy();

        $contractDetails;

        if ($detail['WORKING_CYCLE'] == 'W') {
            $custContractWeekdaysRepo = new \Customer\Repository\CustContractWeekdaysRepo($this->adapter);
            $contractDetails = $custContractWeekdaysRepo->fetchById($id);
        }

        if ($detail['WORKING_CYCLE'] == 'R') {
            $custContractDatesRepo = new \Customer\Repository\CustContractDatesRepo($this->adapter);
            $contractDetails = $custContractDatesRepo->fetchById($id);
        }

        $customerContract->exchangeArrayFromDB($detail);
        $form->bind($customerContract);


        return new ViewModel([
            'form' => $form,
            'id' => $id,
            'customRenderer' => Helper::renderCustomView(),
            'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"]),
            'contractDetails' => $contractDetails,
            'contractEmpDetails' => $custEmployeeDetails
        ]);
    }

}
