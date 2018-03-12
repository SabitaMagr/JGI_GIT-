<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Model\ShiftSetup;
use Customer\Model\CustomerContractDetailModel;
use Customer\Repository\CustomerContractDetailRepo;
use Customer\Repository\CustomerContractRepo;
use Setup\Model\Designation;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class CustomerContractDetails extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(CustomerContractDetailRepo::class);
    }

    public function indexAction() {
        
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('customer-contract');
        }

        $contractDetail = $this->repository->fetchAllContractDetailByContractId($id);
//        echo '<Pre>';
//        print_r($contractDetail);
//        die();

        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    "id" => $id,
                    'contractDetail' => $contractDetail,
                    'designationList' => EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, [Designation::DESIGNATION_ID, Designation::DESIGNATION_TITLE], [Designation::STATUS => "E"]),
                    'shiftList' => EntityHelper::getTableList($this->adapter, ShiftSetup::TABLE_NAME, [ShiftSetup::SHIFT_ID, ShiftSetup::SHIFT_ENAME], [ShiftSetup::STATUS => "E"]),
        ]);
    }

    public function addAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("customer-contract");
        }
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost();

            $designation = $request->getPost('designation');
            $quantity = $request->getPost('quantity');
            $rate = $request->getPost('rate');
            $shift = $request->getPost('shift');
            $weekDayValue = $request->getPost('weekDayValue');
            $daysInMonth = $request->getPost('daysInMonth');

//            echo '<pre>';
//            print_r($postData);
//            print_r($designation);
//            print_r($quantity);
//            print_r($rate);
//            print_r($shift);
//            print_r($weekDayValue);
//            print_r($daysInMonth);
//            die();



            if ($designation) {
                $customerContractRepo = new CustomerContractRepo($this->adapter);
                $contractDetails = $customerContractRepo->fetchById($id);

                $customerId = $contractDetails['CUSTOMER_ID'];

                $contractDetailModel = new CustomerContractDetailModel();
                $contractDetailModel->contractId = $id;
                $contractDetailModel->status = 'D';
                $contractDetailModel->modifiedDt = Helper::getcurrentExpressionDate();
                $contractDetailModel->modifiedBy = $this->employeeId;

                //to delete old assigned
                $this->repository->edit($contractDetailModel, $id);

                $contractDetailModel->customerId = $customerId;

                $i = 0;
                foreach ($designation as $designationDetails) {
                    if ($designationDetails > 0) {
                        $contractDetailModel->designationId = $designationDetails;
                        $contractDetailModel->quantity = $quantity[$i];
                        $contractDetailModel->rate = $rate[$i];
                        $contractDetailModel->shiftId = $shift[$i];
                        $contractDetailModel->weekDetails = $weekDayValue[$i];
                        $contractDetailModel->daysInMonth = $daysInMonth[$i];
                        $contractDetailModel->status = 'E';
                        $contractDetailModel->createdBy = $this->employeeId;
                        $contractDetailModel->modifiedDt = NULL;
                        $contractDetailModel->modifiedBy = NULL;
                        $this->repository->add($contractDetailModel);
                    }
                    $i++;
                }
            }
            $this->flashmessenger()->addMessage("Contract Details Sucessfully Updated");
            $this->redirect()->toRoute("customer-contract");
        }
    }

}
