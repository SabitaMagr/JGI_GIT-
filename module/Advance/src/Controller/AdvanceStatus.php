<?php
namespace Advance\Controller;

use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Advance\Repository\AdvanceStatusRepository;
use ManagerService\Repository\AdvanceApproveRepository;
use SelfService\Repository\AdvanceRequestRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Form\AdvanceRequestForm;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Model\AdvanceRequest;
use Setup\Repository\AdvanceRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Form\Element\Select;
use Setup\Model\ServiceEventType;
use Setup\Model\Advance;
use Zend\Authentication\AuthenticationService;
use Setup\Repository\RecommendApproveRepository;
use Setup\Repository\EmployeeRepository;

class AdvanceStatus extends AbstractActionController
{
    private $adapter;
    private $advanceApproveRepository;
    private $advanceStatusRepository;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->advanceApproveRepository = new AdvanceApproveRepository($adapter);
        $this->advanceStatusRepository = new AdvanceStatusRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId =  $auth->getStorage()->read()['employee_id'];       
    }
    
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new AdvanceRequestForm();
        $this->form = $builder->createForm($form);
    }

    
    public function indexAction() {
        $advanceFormElement = new Select();
        $advanceFormElement->setName("advance");
        $advances = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Advance::TABLE_NAME, Advance::ADVANCE_ID, [Advance::ADVANCE_NAME], [Advance::STATUS => 'E'], "ADVANCE_NAME", "ASC",NULL,FALSE,TRUE);
        $advances1 = [-1 => "All"] + $advances;
        $advanceFormElement->setValueOptions($advances1);
        $advanceFormElement->setAttributes(["id" => "advanceId", "class" => "form-control"]);
        $advanceFormElement->setLabel("Advance Type");

        $advanceStatus = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected',
            'C'=>'Cancelled'
        ];
        $advanceStatusFormElement = new Select();
        $advanceStatusFormElement->setName("advanceStatus");
        $advanceStatusFormElement->setValueOptions($advanceStatus);
        $advanceStatusFormElement->setAttributes(["id" => "advanceRequestStatusId", "class" => "form-control"]);
        $advanceStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'advances' => $advanceFormElement,
                    'advanceStatus' => $advanceStatusFormElement,
                    'searchValues'=> EntityHelper::getSearchData($this->adapter)
        ]);
    }
    public function viewAction() {
        $this->initializeForm();
        $advanceRequestRepository = new AdvanceRequestRepository($this->adapter);
        $advanceApproveRepository = new AdvanceApproveRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("advanceStatus");
        }
        $advanceRequest = new AdvanceRequest();
        $request = $this->getRequest();

        $detail = $this->advanceApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $employeeId = $detail['EMPLOYEE_ID'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);
        $recommApprove = 0;
        if($empRecommendApprove['RECOMMEND_BY']==$empRecommendApprove['APPROVED_BY']){
            $recommApprove=1;
        }
        
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };
        
        $employeeName = $fullName($detail['EMPLOYEE_ID']);        
        $authRecommender = ($status=='RQ' || $status=='C')? $detail['RECOMMENDER'] : $detail['RECOMMENDED_BY'];
        $authApprover = ($status=='RC' || $status=='C' || $status=='RQ' || ($status=='R' && $approvedDT==null))? $detail['APPROVER'] : $detail['APPROVED_BY'];

        if (!$request->isPost()) {
            $advanceRequest->exchangeArrayFromDB($detail);
            $this->form->bind($advanceRequest);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $advanceRequest->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $advanceRequest->status = "R";
                $this->flashmessenger()->addMessage("Advance Request Rejected!!!");
            } else if ($action == "Approve") {
                $advanceRequest->status = "AP";
                $this->flashmessenger()->addMessage("Advamce Request Approved");
            }
            $advanceRequest->approvedBy = $this->employeeId;
            $advanceRequest->approvedRemarks = $reason;
            $this->advanceApproveRepository->edit($advanceRequest, $id);

            return $this->redirect()->toRoute("advanceStatus");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeId' => $employeeId,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DATE'],
                    'recommender' => $fullName($authRecommender),
                    'approver' => $fullName($authApprover),
                    'approvedDT'=>$detail['APPROVED_DATE'],
                    'status' => $status,
                    'advances' => EntityHelper::getTableKVListWithSortOption($this->adapter, Advance::TABLE_NAME, Advance::ADVANCE_ID, [Advance::ADVANCE_NAME], [Advance::STATUS => "E"], Advance::ADVANCE_ID, "ASC",NULL,FALSE,TRUE),
                    'customRenderer' => Helper::renderCustomView(),
                    'recommApprove'=>$recommApprove
        ]);
    }
    
}