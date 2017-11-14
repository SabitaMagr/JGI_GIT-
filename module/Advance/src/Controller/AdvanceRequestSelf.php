<?php

namespace Advance\Controller;

use Advance\Form\AdvanceRequestForm;
use Advance\model\AdvanceRequestModel;
use Advance\model\AdvanceSetupModel;
use Advance\Repository\AdvanceRequestSelfRepository;
use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class AdvanceRequestSelf extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(AdvanceRequestSelfRepository::class);
        $this->initializeForm(AdvanceRequestForm::class);
    }

    public function indexAction() {

        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $rawList = $this->repository->getfilterRecords($data);
                $list = Helper::extractDbData($rawList);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }


        return Helper::addFlashMessagesToArray($this, [
                    'employeeId' => $this->employeeId,
                    'acl' => $this->acl
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);

            if ($this->form->isValid()) {
                $advanceRequestModel = new AdvanceRequestModel();
                $advanceRequestModel->exchangeArrayFromForm($this->form->getData());

                $advanceRequestModel->deductionType = $postData['deductionType'];
                $advanceRequestModel->advanceRequestId = (int) Helper::getMaxId($this->adapter, AdvanceRequestModel::TABLE_NAME, AdvanceRequestModel::ADVANCE_REQUEST_ID) + 1;
                $advanceRequestModel->status = "RQ";

                $this->repository->add($advanceRequestModel);
                $this->flashmessenger()->addMessage("Advance Request Successfully added!!!");

                return $this->redirect()->toRoute("advance-request-self");
            }
        }

        $basicSalary = EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, ['SALARY'], [HrEmployees::EMPLOYEE_ID => $this->employeeId]);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeId' => $this->employeeId,
                    'advance' => EntityHelper::getTableList($this->adapter, AdvanceSetupModel::TABLE_NAME, ['*'], [AdvanceSetupModel::STATUS => 'E']),
                    'customRenderer' => Helper::renderCustomView(),
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"]),
                    'salary' => $basicSalary[0]['SALARY']
        ]);
    }

    public function viewAction() {

        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id === 0) {
            return $this->redirect()->toRoute("advance-request-self");
        }
        
        $detail = $this->repository->fetchById($id);
        
//        $leaveApproveRepository = new LeaveApproveRepository($this->adapter);
//
//        $detail = $leaveApproveRepository->fetchById($id);
//
//
//        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
//        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
//
//        //to get the previous balance of selected leave from assigned leave detail
//        $result = $leaveApproveRepository->assignedLeaveDetail($detail['LEAVE_ID'], $detail['EMPLOYEE_ID']);
//        $preBalance = $result['BALANCE'];
//
//        $leaveApply = new LeaveApply();
//        $leaveApply->exchangeArrayFromDB($detail);
//        $this->form->bind($leaveApply);
        
//        $this->initializeForm();
//        $this->getRecommendApprover();
//        $id = (int) $this->params()->fromRoute('id');
//
//        if ($id === 0) {
//            return $this->redirect()->toRoute("advanceRequest");
//        }
//        $fullName = function($id) {
//            $empRepository = new EmployeeRepository($this->adapter);
//            $empDtl = $empRepository->fetchById($id);
//            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
//            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
//        };
//
//        $recommenderName = $fullName($this->recommender);
//        $approverName = $fullName($this->approver);
//
//        $model = new AdvanceRequestModel();
//        $detail = $this->repository->fetchById($id);
//        $status = $detail['STATUS'];
//        $approvedDT = $detail['APPROVED_DATE'];
//        $recommended_by = $fullName($detail['RECOMMENDED_BY']);
//        $approved_by = $fullName($detail['APPROVED_BY']);
//        $authRecommender = ($status == 'RQ' || $status == 'C') ? $recommenderName : $recommended_by;
//        $authApprover = ($status == 'RC' || $status == 'RQ' || $status == 'C' || ($status == 'R' && $approvedDT == null)) ? $approverName : $approved_by;
//
//        $model->exchangeArrayFromDB($detail);
//        $this->form->bind($model);
//
//        $employeeName = $fullName($detail['EMPLOYEE_ID']);

        

//        return Helper::addFlashMessagesToArray($this, [
//                    'form' => $this->form,
//                    'employeeName' => $employeeName,
//                    'employeeId' => $detail['EMPLOYEE_ID'],
//                    'status' => $detail['STATUS'],
//                    'requestedDate' => $detail['REQUESTED_DATE'],
//                    'recommender' => $authRecommender,
//                    'approver' => $authApprover,
//                    'advances' => LoanAdvanceHelper::getAdvanceList($this->adapter, $this->employeeId),
//                    'advanceRequestData' => $detail
//        ]);
    }

}
