<?php
namespace Travel\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Helper\NumberHelper;
use Exception;
use ManagerService\Repository\TravelApproveRepository;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelExpenseDetail;
use SelfService\Model\TravelRequest;
use SelfService\Repository\TravelExpenseDtlRepository;
use SelfService\Repository\TravelRequestRepository;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Travel\Repository\TravelStatusRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class TravelStatus extends AbstractActionController
{
    private $adapter;
    private $travelApproveRepository;
    private $travelStatusRepository;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->travelApproveRepository = new TravelApproveRepository($adapter);
        $this->travelStatusRepository = new TravelStatusRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId =  $auth->getStorage()->read()['employee_id'];       
    }
    
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new TravelRequestForm();
        $this->form = $builder->createForm($form);
    }
   
    public function indexAction() {
        $travelStatus = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected',
            'C'=>'Cancelled'
        ];
        $travelStatusFormElement = new Select();
        $travelStatusFormElement->setName("travelStatus");
        $travelStatusFormElement->setValueOptions($travelStatus);
        $travelStatusFormElement->setAttributes(["id" => "travelRequestStatusId", "class" => "form-control"]);
        $travelStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'travelStatus' => $travelStatusFormElement,
                    'searchValues'=> EntityHelper::getSearchData($this->adapter)
        ]);
    }
    public function viewAction() {
        $this->initializeForm();
        $travelRequestRepository = new TravelRequestRepository($this->adapter);
        $travelApproveRepository = new TravelApproveRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus");
        }
        $travelRequest = new TravelRequest();
        $request = $this->getRequest();

        $detail = $this->travelApproveRepository->fetchById($id);
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
        
        $empRepository = new EmployeeRepository($this->adapter);
        $approverFlag =($detail['APPROVER_ROLE']=='DCEO')? [HrEmployees::IS_DCEO=>'Y']:[HrEmployees::IS_CEO=>'Y'];
        $whereCondition = array_merge([HrEmployees::STATUS=>'E', HrEmployees::RETIRED_FLAG=>'N'],$approverFlag);
        $approverDetail = $empRepository->fetchByCondition($whereCondition);
        
        $employeeName = $fullName($detail['EMPLOYEE_ID']);        
        $authRecommender = ($status=='RQ' || $status=='C')? $detail['RECOMMENDER'] : $detail['RECOMMENDED_BY'];
        $authApprover = ($status=='RC' || $status=='C' || $status=='RQ' || ($status=='R' && $approvedDT==null))? $approverDetail['EMPLOYEE_ID'] : $detail['APPROVED_BY'];


        if (!$request->isPost()) {
            $travelRequest->exchangeArrayFromDB($detail);
            $this->form->bind($travelRequest);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $travelRequest->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $travelRequest->status = "R";
                $this->flashmessenger()->addMessage("Travel Request Rejected!!!");
            } else if ($action == "Approve") {
                $travelRequest->status = "AP";
                $this->flashmessenger()->addMessage("Travel Request Approved");
            }
            $travelRequest->approvedBy = $this->employeeId;
            $travelRequest->approvedRemarks = $reason;
            $this->travelApproveRepository->edit($travelRequest, $id);

            return $this->redirect()->toRoute("travelStatus");
        }
        $requestType = array(
            'ad'=>'Advance',
            'ep'=>'Expense'
        );
        if($detail['REFERENCE_TRAVEL_ID']!=null){
            $referenceTravelDtl = $this->travelApproveRepository->fetchById($detail['REFERENCE_TRAVEL_ID']);
            $advanceAmt = $referenceTravelDtl['REQUESTED_AMOUNT'];
        }else{
            $advanceAmt = 0 ;
        }
        $transportTypes = array(
            'AP'=>'Aero Plane',
            'OV'=>'Office Vehicles',
            'TI'=>'Taxi',
            'BS'=>'Bus'
        );
        $vehicle = '';
        foreach($transportTypes as $key=>$value){
            if($detail['TRANSPORT_TYPE']==$key){
                $vehicle = $value;
            }
        }
        $empRepository = new EmployeeRepository($this->adapter);
        $empDtl = $empRepository->fetchForProfileById($detail['EMPLOYEE_ID']);
        
        $numberInWord = new NumberHelper();
        $advanceAmount = $numberInWord->toText($detail['REQUESTED_AMOUNT']);
        $subDetail = [];
        if($detail['SUB_EMPLOYEE_ID']!=null){
            $subEmpDetail = $empRepository->fetchForProfileById($detail['SUB_EMPLOYEE_ID']);
            $subDetail = [
              'SUB_EMPLOYEE_NAME'=>  $fullName($detail['SUB_EMPLOYEE_ID']),
              'SUB_DESIGNATION'=> $subEmpDetail['DESIGNATION'],
              'SUB_APPROVED_DATE'=>$detail['SUB_APPROVED_DATE']
            ];
        }
        $duration = ($detail['TO_DATE']-$detail['FROM_DATE'])+1;
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'requestType'=>$requestType,
                    'employeeId' => $employeeId,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DATE'],
                    'recommender' => $fullName($authRecommender),
                    'approver' => $fullName($authApprover),
                    'approvedDT'=>$detail['APPROVED_DATE'],
                    'status' => $status,
                    'advanceAmt'=> $advanceAmt, 
                    'transportTypes'=>$transportTypes,
                    'recommApprove'=>$recommApprove,
                    'subEmployeeId'=> $detail['SUB_EMPLOYEE_ID'],
                    'subRemarks'=>$detail['SUB_REMARKS'],
                    'subApprovedFlag'=>$detail['SUB_APPROVED_FLAG'],
                    'empDtl'=>$empDtl,
                    'detail'=>$detail,
                    'todayDate'=>date('d-M-Y'),
                    'vehicle'=>$vehicle,
                    'advanceAmount'=>$advanceAmount,
                    'subDetail'=>$subDetail,
                    'duration'=>$duration,
                    'customRender' => Helper::renderCustomView(),
                    'employeeList'=>  EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME],[HrEmployees::STATUS => "E",HrEmployees::RETIRED_FLAG => "N"], HrEmployees::FIRST_NAME, "ASC", " ",false,true)
        
        ]);
    }   
    public function expenseDetailAction(){
        $this->initializeForm();
        $travelRequestRepository = new TravelRequestRepository($this->adapter);
        $travelApproveRepository = new TravelApproveRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelStatus");
        }
        $travelRequest = new TravelRequest();
        $request = $this->getRequest();

        $detail = $this->travelApproveRepository->fetchById($id);
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
        $empRepository = new EmployeeRepository($this->adapter);
        $approverFlag =($detail['APPROVER_ROLE']=='DCEO')? [HrEmployees::IS_DCEO=>'Y']:[HrEmployees::IS_CEO=>'Y'];
        $whereCondition = array_merge([HrEmployees::STATUS=>'E', HrEmployees::RETIRED_FLAG=>'N'],$approverFlag);
        $approverDetail = $empRepository->fetchByCondition($whereCondition);
        
        $employeeName = $fullName($detail['EMPLOYEE_ID']);        
        $authRecommender = ($status=='RQ' || $status=='C')? $detail['RECOMMENDER'] : $detail['RECOMMENDED_BY'];
        $authApprover = ($status=='RC' || $status=='C' || $status=='RQ' || ($status=='R' && $approvedDT==null))? $approverDetail['EMPLOYEE_ID'] : $detail['APPROVED_BY'];

        if($detail['REFERENCE_TRAVEL_ID']!=null){
            $referenceTravelDtl = $this->travelApproveRepository->fetchById($detail['REFERENCE_TRAVEL_ID']);
            $advanceAmt = $referenceTravelDtl['REQUESTED_AMOUNT'];
        }else{
            $advanceAmt = 0 ;
        }
        $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
        $expenseDtlList = [];
        $result = $expenseDtlRepo->fetchByTravelId($id);
        $totalAmount=0;
        foreach($result as $row){
            $totalAmount+=$row['TOTAL_AMOUNT'];
            array_push($expenseDtlList, $row);
        }
        $transportType = [
            "AP"=>"Aero Plane",
            "OV"=>"Office Vehicles",
            "TI"=>"Taxi",
            "BS"=>"Bus"
        ];
        $numberInWord = new NumberHelper();
        $totalExpense = $numberInWord->toText($totalAmount);
        
        $empRepository = new EmployeeRepository($this->adapter);
        $empDtl = $empRepository->fetchForProfileById($detail['EMPLOYEE_ID']);
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
                    'advanceAmt'=> $advanceAmt,
                    'recommApprove'=>$recommApprove,
                    'expenseDtlList'=>$expenseDtlList,
                    'transportType'=>$transportType,
                    'todayDate'=>date('d-M-Y'),
                    'detail'=>$detail,
                    'empDtl'=>$empDtl,
                    'totalExpense'=>$totalExpense
        ]);
    }
    
     public function checkSettlementAction() {
        $request = $this->getRequest();
        $model = new TravelRequest();
        
        $travelRequestRepo = new TravelRequestRepository($this->adapter);
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $expenseDtlList = $postData['data']['expenseDtlList'];
            $departureDate = $postData['data']['departureDate'];
            $returnedDate = $postData['data']['returnedDate'];
            $requestedType = $postData['data']['requestedType'];
            $sumAllTotal = (float) $postData['data']['sumAllTotal'];
            $travelId = (int) $postData['data']['travelId'];
            $approverRole = $postData['data']['approverRole'];
            $detail = $this->travelApproveRepository->fetchById($travelId);
            $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
            $expenseDtlModel = new TravelExpenseDetail();

            $requestedAmt = $sumAllTotal;
            $travelRequestRepo->updateDates($departureDate, $returnedDate, $requestedAmt, $travelId);
            $travelRequestRepo->updateStatus('SC',$travelId);
            foreach ($expenseDtlList as $expenseDtl) {
                $transportType = $expenseDtl['transportType'];
                $id = (int) $expenseDtl['id'];
                $expenseDtlModel->departureDate = Helper::getExpressionDate($expenseDtl['departureDate']);
                $expenseDtlModel->departurePlace = $expenseDtl['departurePlace'];
                $expenseDtlModel->departureTime = Helper::getExpressionTime($expenseDtl['departureTime']);
                $expenseDtlModel->destinationDate = Helper::getExpressionDate($expenseDtl['destinationDate']);
                $expenseDtlModel->destinationPlace = $expenseDtl['destinationPlace'];
                $expenseDtlModel->destinationTime = Helper::getExpressionTime($expenseDtl['destinationTime']);
                $expenseDtlModel->transportType = $transportType['id'];
                $expenseDtlModel->fare = (float) $expenseDtl['fare'];
                $expenseDtlModel->allowance = ($expenseDtl['allowance'] != null) ? (float) $expenseDtl['allowance'] : null;
                $expenseDtlModel->localConveyence = ($expenseDtl['localConveyence'] != null) ? (float) $expenseDtl['localConveyence'] : null;
                $expenseDtlModel->miscExpenses = ($expenseDtl['miscExpense'] != null) ? (float) $expenseDtl['miscExpense'] : null;
                $expenseDtlModel->totalAmount = (float) $expenseDtl['total'];
                $expenseDtlModel->remarks = ($expenseDtl['remarks'] != null) ? $expenseDtl['remarks'] : null;
                $expenseDtlModel->status = 'E';
                $expenseDtlModel->fareFlag = ($expenseDtl['fareFlag']=="true" && $expenseDtl['fareFlag']!="")?'Y':'N';
                $expenseDtlModel->allowanceFlag = ($expenseDtl['allowanceFlag']=="true" && $expenseDtl['allowanceFlag']!="")?'Y':'N';
                $expenseDtlModel->localConveyenceFlag = ($expenseDtl['localConveyenceFlag']=="true" && $expenseDtl['localConveyenceFlag']!="")?'Y':'N';
                $expenseDtlModel->miscExpensesFlag = ($expenseDtl['miscExpenseFlag']=="true" && $expenseDtl['miscExpenseFlag']!="")?'Y':'N';
                if ($id == 0) {
                    $expenseDtlModel->id = ((int) Helper::getMaxId($this->adapter, TravelExpenseDetail::TABLE_NAME, TravelExpenseDetail::ID)) + 1;
                    $expenseDtlModel->travelId = ($requestedType == 'ad') ? $model->travelId : $travelId;
                    $expenseDtlModel->createdBy = $this->employeeId;
                    $expenseDtlModel->createdDate = Helper::getcurrentExpressionDate();
                    $expenseDtlRepo->add($expenseDtlModel);
                } else {
                    $expenseDtlModel->modifiedBy = (int) $this->employeeId;
                    $expenseDtlModel->modifiedDate = Helper::getcurrentExpressionDate();
                    $expenseDtlRepo->edit($expenseDtlModel, $id);
                }
            }
            try {
                //HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $model, $this->adapter, $this->plugin('url'));
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage($e->getMessage());
            }
            return new CustomViewModel(['success' => true, 'data' => ['msg' => 'Travel Request Successfully added!!!']]);
        } else {
            $id = (int) $this->params()->fromRoute('id');
            if ($id === 0) {
                return $this->redirect()->toRoute("travelRequest");
            }
            $detail = $travelRequestRepo->fetchById($id);
            $travelId = ($detail['REQUESTED_TYPE'] == 'ep') ? $detail['REFERENCE_TRAVEL_ID'] : $id;
            $referenceDetail = $travelRequestRepo->fetchById($travelId);
            return Helper::addFlashMessagesToArray($this, [
                        'form' => $this->form,
                        'advanceAmt' => $referenceDetail['REQUESTED_AMOUNT'],
                        'detail' => $referenceDetail,
                        'id' => $id,
                        'requestedType' => $detail['REQUESTED_TYPE']
            ]);
        }
    }
}