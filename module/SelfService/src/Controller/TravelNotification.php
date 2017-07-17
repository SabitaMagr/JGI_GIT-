<?php
namespace SelfService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Exception;
use Application\Helper\Helper;
use SelfService\Model\TravelSubstitute;
use Zend\Authentication\AuthenticationService;
use Setup\Repository\EmployeeRepository;
use ManagerService\Repository\TravelApproveRepository;
use Travel\Repository\TravelStatusRepository;
use SelfService\Form\TravelRequestForm;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Model\TravelRequest;
use SelfService\Repository\TravelSubstituteRepository;
use SelfService\Repository\TravelRequestRepository;
use Application\Helper\EntityHelper;
use Setup\Model\HrEmployees;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;

class TravelNotification extends AbstractActionController{
    private $adapter;
    private $repository;
    private $employeeId;
    private $form;
    
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
        $this->repository = new TravelSubstituteRepository($this->adapter);
    }
    
    public function indexAction() {
        $result = $this->repository->fetchByEmployeeId($this->employeeId);
        $list = [];
        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };
        
        $getValueApp = function($approvedFlag){
            if ($approvedFlag == "Y") {
                return "Yes";
            } else if ($approvedFlag == 'N') {
                return "No";
            } 
        };
         $getRequestedType = function($requestedType) {
            if ($requestedType == 'ad') {
                return 'Advance';
            } else if ($requestedType == 'ep') {
                return 'Expense';
            }
        };
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };
        
        foreach($result as $row){
            $status = $getValue($row['STATUS']);
            
            $statusId = $row['STATUS'];
            $approvedDT = $row['APPROVED_DATE'];

            $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
            $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];
            
            $recommenderName = $fullName($authRecommender);
            $approverName = $fullName($authApprover);
            $subEmployeeName = $fullName($row['SUB_EMPLOYEE_ID']);
            $employeeName = $fullName($row['EMPLOYEE_ID']);

            $new_row = array_merge($row, [
                'STATUS' => $status,
                'APPROVER_NAME' => $approverName,
                'RECOMMENDER_NAME' => $recommenderName,
                'SUB_EMPLOYEE_NAME'=>$subEmployeeName,
                'EMPLOYEE_NAME'=>$employeeName,
                'REQUESTED_TYPE' => $getRequestedType($row['REQUESTED_TYPE']),
                'SUB_APPROVED_FLAG'=>$getValueApp($row['SUB_APPROVED_FLAG'])
            ]);
            array_push($list, $new_row);
        }
        return Helper::addFlashMessagesToArray($this, [
            'list'=>$list
        ]);
    }
    public function initializeForm() {
        $travelRequestForm = new TravelRequestForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($travelRequestForm);
    }
    
     public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $travelApproveRepository = new TravelApproveRepository($this->adapter);
        
        $travelRequestRepository = new TravelRequestRepository($this->adapter);
        
        $travelSubstituteDetail = $this->repository->fetchById($id);

        if ($id === 0) {
            return $this->redirect()->toRoute("travelNotification");
        }

        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };
        
        $travelRequest = new TravelRequest();
        $request = $this->getRequest();

        $detail = $travelApproveRepository->fetchById($id);
        $recommenderName = $fullName($detail['RECOMMENDER']);
        $approverName = $fullName($detail['APPROVER']);

        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];
        $recommended_by = $fullName($detail['RECOMMENDED_BY']);
        $approved_by = $fullName($detail['APPROVED_BY']);
        $authRecommender = ($status == 'RQ' || $status == 'C') ? $recommenderName : $recommended_by;
        $authApprover = ($status == 'RC' || $status == 'RQ' || $status == 'C' || ($status == 'R' && $approvedDT == null)) ? $approverName : $approved_by;
        $employeeName = $fullName($detail['EMPLOYEE_ID']);

        if (!$request->isPost()) {
            $travelRequest->exchangeArrayFromDB($detail);
            $this->form->bind($travelRequest);
        }else {
            $travelSubstituteModel = new TravelSubstitute();
            $getData = $request->getPost();
            $action = $getData->submit;
            $travelSubstituteModel->approvedDate = Helper::getcurrentExpressionDate();
            $travelSubstituteModel->remarks = $getData->subRemarks;
            if($action=='Approve'){
                $travelSubstituteModel->approvedFlag = "Y";
                $this->flashmessenger()->addMessage("Substitute Work Request Approved!!!");
            }else if($action=='Reject'){
                $travelSubstituteModel->approvedFlag = "N";
                $travelRequestRepository->delete($id);
                $this->flashmessenger()->addMessage("Substitute Work Request Rejected!!!");
            }
            $this->repository->edit($travelSubstituteModel, $id);
            $travelRequest->travelId = $id;
            try {
                HeadNotification::pushNotification(($travelSubstituteModel->approvedFlag == 'Y') ? NotificationEvents::TRAVEL_SUBSTITUTE_ACCEPTED : NotificationEvents::TRAVEL_SUBSTITUTE_REJECTED, $travelRequest, $this->adapter, $this->plugin('url'));
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage($e->getMessage());
            }
            if($action=='Approve'){
                try {
                    HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $travelRequest, $this->adapter, $this->plugin('url'));
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            $this->redirect()->toRoute('travelNotification');
        }
        $transportTypes = array(
            'AP'=>'Flight',
            'OV'=>'Office Vehicles',
            'TI'=>'Taxi',
            'BS'=>'Bus'
        );
        $requestType = array(
            'ad' => 'Advance',
            'ep' => 'Expense'
        );
        if($detail['REFERENCE_TRAVEL_ID']!=null){
            $referenceTravelDtl = $travelRequestRepository->fetchById($detail['REFERENCE_TRAVEL_ID']);
            $advanceAmt = $referenceTravelDtl['REQUESTED_AMOUNT'];
        }else{
            $advanceAmt = 0 ;
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id'=>$id,
                    'requestTypes' => $requestType,
                    'employeeName' => $employeeName,
                    'status' => $detail['STATUS'],
                    'requestedDate' => $detail['REQUESTED_DATE'],
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'advanceAmt'=>$advanceAmt,
                    'transportTypes'=>$transportTypes,
                    'subEmployeeId'=> $detail['SUB_EMPLOYEE_ID'],
                    'subRemarks'=>$detail['SUB_REMARKS'],
                    'subApprovedFlag'=>$detail['SUB_APPROVED_FLAG'],
                    'employeeList'=>  EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME],[HrEmployees::STATUS => "E",HrEmployees::RETIRED_FLAG => "N"], HrEmployees::FIRST_NAME, "ASC", " ",false,true)
        ]);
    }
}