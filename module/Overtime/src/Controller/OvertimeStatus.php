<?php
namespace Overtime\Controller;

use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Overtime\Repository\OvertimeStatusRepository;
use ManagerService\Repository\OvertimeApproveRepository;
use SelfService\Repository\OvertimeDetailRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Form\OvertimeRequestForm;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Model\Overtime;
use Zend\Form\Element\Select;
use Zend\Authentication\AuthenticationService;
use Setup\Repository\RecommendApproveRepository;

class OvertimeStatus extends AbstractActionController
{
    private $adapter;
    private $overtimeApproveRepository;
    private $overtimeStatusRepository;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->overtimeApproveRepository = new OvertimeApproveRepository($adapter);
        $this->overtimeStatusRepository = new OvertimeStatusRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId =  $auth->getStorage()->read()['employee_id'];       
    }
    
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new OvertimeRequestForm();
        $this->form = $builder->createForm($form);
    }

    
    public function indexAction() {
        $status = [
            '-1' => 'All',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected',
            'C'=>'Cancelled'
        ];
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "requestStatusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'status' => $statusFormElement,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }
    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("overtimeStatus");
        }
        $overtimeModel = new Overtime();
        $request = $this->getRequest();

        $detail = $this->overtimeApproveRepository->fetchById($id);
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
        
        $employeeName = $detail['FIRST_NAME'] . " " . $detail['MIDDLE_NAME'] . " " . $detail['LAST_NAME'];        
        $RECM_MN = ($detail['RECM_MN']!=null)? " ".$detail['RECM_MN']." ":" ";
        $recommender = $detail['RECM_FN'].$RECM_MN.$detail['RECM_LN'];        
        $APRV_MN = ($detail['APRV_MN']!=null)? " ".$detail['APRV_MN']." ":" ";
        $approver = $detail['APRV_FN'].$APRV_MN.$detail['APRV_LN'];
        $MN1 = ($detail['MN1']!=null)? " ".$detail['MN1']." ":" ";
        $recommended_by = $detail['FN1'].$MN1.$detail['LN1'];        
        $MN2 = ($detail['MN2']!=null)? " ".$detail['MN2']." ":" ";
        $approved_by = $detail['FN2'].$MN2.$detail['LN2'];
        $authRecommender = ($status=='RQ' || $status=='C')?$recommender:$recommended_by;
        $authApprover = ($status=='RC' || $status=='C' || $status=='RQ' || ($status=='R' && $approvedDT==null))?$approver:$approved_by;


        if (!$request->isPost()) {
            $overtimeModel->exchangeArrayFromDB($detail);
            $this->form->bind($overtimeModel);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $overtimeModel->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $overtimeModel->status = "R";
                $this->flashmessenger()->addMessage("Overtime Request Rejected!!!");
            } else if ($action == "Approve") {
                $overtimeModel->status = "AP";
                $this->flashmessenger()->addMessage("Overtime Request Approved");
            }
            $overtimeModel->approvedBy = $this->employeeId;
            $overtimeModel->approvedRemarks = $reason;
            $this->overtimeApproveRepository->edit($overtimeModel, $id);

            return $this->redirect()->toRoute("overtimeStatus");
        }
        $overtimeDetailRepo = new OvertimeDetailRepository($this->adapter);
        $overtimeDetailResult =$overtimeDetailRepo->fetchByOvertimeId($detail['OVERTIME_ID']);
        $overtimeDetails = [];
        foreach($overtimeDetailResult as $overtimeDetailRow){
            array_push($overtimeDetails,$overtimeDetailRow);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeId' => $employeeId,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DATE'],
                    'recommender' => $authRecommender,
                    'approvedDT'=>$detail['APPROVED_DATE'],
                    'approver' => $authApprover,
                    'status' => $status,
                    'customRenderer' => Helper::renderCustomView(),
                    'recommApprove'=>$recommApprove,
                    'overtimeDetails'=>$overtimeDetails
        ]);
    }
    
    public function calculateAction(){
        print_r("hellow"); die();
    }
}