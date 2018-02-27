<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Model\ShiftSetup;
use Customer\Model\CustomerContractDetailModel;
use Customer\Model\CustomerLocationModel;
use Customer\Repository\CustomerContractRepo;
use Setup\Model\Designation;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class CustomerContractDetails extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
//         $this->initializeRepository(CustomerLocationRepo::class);
    }

    public function indexAction() {
        
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('customer-contract');
        }

        $customerContractRepo = new CustomerContractRepo($this->adapter);
        $contractDetails = $customerContractRepo->fetchById($id);
        $customerId = $contractDetails['CUSTOMER_ID'];
        
        

        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    "id" => $id,
                    "customerId" => $customerId,
                    'designationList' => EntityHelper::getTableList($this->adapter, Designation::TABLE_NAME, [Designation::DESIGNATION_ID, Designation::DESIGNATION_TITLE], [Designation::STATUS => "E"]),
                    'shiftList' => EntityHelper::getTableList($this->adapter, ShiftSetup::TABLE_NAME, [ShiftSetup::SHIFT_ID, ShiftSetup::SHIFT_ENAME], [ShiftSetup::STATUS => "E"]),
                    'locationList' => EntityHelper::getTableList($this->adapter, CustomerLocationModel::TABLE_NAME, [CustomerLocationModel::LOCATION_ID, CustomerLocationModel::LOCATION_NAME], [CustomerLocationModel::STATUS => "E", CustomerLocationModel::CUSTOMER_ID => $customerId])
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

            echo '<pre>';
            print_r($designation);
            print_r($quantity);
            print_r($rate);
            print_r($shift);


            if ($designation) {
                $customerContractRepo = new CustomerContractRepo($this->adapter);
                $contractDetails = $customerContractRepo->fetchById($id);

                $customerId = $contractDetails['CUSTOMER_ID'];

                $contractDetailModel = new CustomerContractDetailModel();
                $contractDetailModel->contractId;
                $contractDetailModel->customerId;


                print_r($contractDetails);

                $i = 0;
                foreach ($designation as $designationDetails) {
                    if ($employeeDetails > 0) {
                        $contractDetailModel->designationId = $designationDetails;
                        $contractDetailModel->quantity = $quantity;
                        $contractDetailModel->rate = $rate;
                        $contractDetailModel->shiftId = $shift;
//                        $contractDetailModel->locationId;
//                        $contractDetailModel->daysInMonth;
                    }
                    $i++;
                }



                die();
                echo 'yes';
            }


            die();
        }
    }

}
