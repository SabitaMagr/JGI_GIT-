<?php

namespace SelfService\Controller;

use Application\Helper\Helper;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelRequest as TravelRequestModel;
use SelfService\Repository\TravelRequestRepository;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Repository\TravelExpenseDtlRepository;
use SelfService\Model\TravelExpenseDetail;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Custom\CustomViewModel;

class TravelRequest extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;
    private $employeeId;
    private $recommender;
    private $approver;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new TravelRequestRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new TravelRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function getRecommendApprover() {
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($this->employeeId);

        if ($empRecommendApprove != null) {
            $this->recommender = $empRecommendApprove['RECOMMEND_BY'];
            $this->approver = $empRecommendApprove['APPROVED_BY'];
        } else {
            $result = $this->recommendApproveList();
            if (count($result['recommender']) > 0) {
                $this->recommender = $result['recommender'][0]['id'];
            } else {
                $this->recommender = null;
            }
            if (count($result['approver']) > 0) {
                $this->approver = $result['approver'][0]['id'];
            } else {
                $this->approver = null;
            }
        }
    }

    public function indexAction() {
        $this->getRecommendApprover();
        $result = $this->repository->getAllByEmployeeId($this->employeeId);
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $recommenderName = $fullName($this->recommender);
        $approverName = $fullName($this->approver);

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
        $getAction = function($status) {
            if ($status == "RQ") {
                return ["delete" => 'Cancel Request'];
            } else {
                return ["view" => 'View'];
            }
        };
        $getRequestedType = function($requestedType) {
            if ($requestedType == 'ad') {
                return 'Advance';
            } else if ($requestedType == 'ep') {
                return 'Expense';
            }
        };
        foreach ($result as $row) {
            $status = $getValue($row['STATUS']);
            $action = $getAction($row['STATUS']);
            $statusID = $row['STATUS'];
            $approvedDT = $row['APPROVED_DATE'];
            $MN1 = ($row['MN1'] != null) ? " " . $row['MN1'] . " " : " ";
            $recommended_by = $row['FN1'] . $MN1 . $row['LN1'];
            $MN2 = ($row['MN2'] != null) ? " " . $row['MN2'] . " " : " ";
            $approved_by = $row['FN2'] . $MN2 . $row['LN2'];
            $authRecommender = ($statusID == 'RQ' || $statusID == 'C') ? $recommenderName : $recommended_by;
            $authApprover = ($statusID == 'RC' || $statusID == 'RQ' || $statusID == 'C' || ($statusID == 'R' && $approvedDT == null)) ? $approverName : $approved_by;

            $new_row = array_merge($row, [
                'RECOMMENDER_NAME' => $authRecommender,
                'APPROVER_NAME' => $authApprover,
                'STATUS' => $status,
                'ACTION' => key($action),
                'REQUESTED_TYPE' => $getRequestedType($row['REQUESTED_TYPE']),
                'ACTION_TEXT' => $action[key($action)]
            ]);
            if($statusID=='RQ'){
                $new_row['ALLOW_TO_EDIT'] = 1;
            }else{
                $new_row['ALLOW_TO_EDIT'] = 0;
            }
            $checkForExpense = $this->repository->fetchByReferenceId($row['TRAVEL_ID']);
            //print_r($checkForExpense); die();
            $new_row['ALLOW_TO_REQUEST_EX'] =($statusID=='AP' && count($checkForExpense)==0) ? 1 : 0;
            array_push($list, $new_row);
        }
        //print_r($list); die();
        return Helper::addFlashMessagesToArray($this, ['list' => $list]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new TravelRequestModel();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->requestedAmount = ($model->requestedAmount==null)?0:$model->requestedAmount;
                $model->travelId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
                $model->employeeId = $this->employeeId;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $this->repository->add($model);
                HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $model, $this->adapter, $this->plugin('url'));
                $this->flashmessenger()->addMessage("Travel Request Successfully added!!!");
                return $this->redirect()->toRoute("travelRequest");
            }
        }
        $requestType = array(
            'ad' => 'Advance'
        );

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'requestTypes' => $requestType
        ]);
    }
    
    public function expenseRequestAction(){
        $request = $this->getRequest();
        $model = new TravelRequestModel();
        if($request->isPost()){
            $postData = $request->getPost()->getArrayCopy();
            $expenseDtlList = $postData['data']['expenseDtlList'];
            $departureDate = $postData['data']['departureDate'];
            $returnedDate = $postData['data']['returnedDate'];
            $requestedType = $postData['data']['requestedType'];
            $travelId = (int)$postData['data']['travelId'];
            $sumAllTotal = (float)$postData['data']['sumAllTotal'];
            $detail = $this->repository->fetchById($travelId);
            $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
            $expenseDtlModel = new TravelExpenseDetail();
            
            $requestedAmt = $sumAllTotal;
            if($requestedType=='ad'){
                $model->travelId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
                $model->employeeId = $this->employeeId;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $model->fromDate = $detail['FROM_DATE'];
                $model->toDate = $detail['TO_DATE'];
                $model->destination = $detail['DESTINATION'];
                $model->purpose = $detail['PURPOSE'];
                $model->travelCode = $detail['TRAVEL_CODE'];
                $model->requestedType = 'ep';
                $model->requestedAmount = $requestedAmt;
                $model->referenceTravelId = $travelId;
                $model->departureDate =  Helper::getExpressionDate($departureDate);
                $model->returnedDate = Helper::getExpressionDate($returnedDate);
                $this->repository->add($model);
            }else if($requestedType=='ep'){
                $this->repository->updateDates($departureDate,$returnedDate,$requestedAmt,$travelId);
            }
            
            foreach($expenseDtlList as $expenseDtl){
                $transportType = $expenseDtl['transportType'];
                $id = (int)$expenseDtl['id'];
               
                $expenseDtlModel->departureDate = Helper::getExpressionDate($expenseDtl['departureDate']);
                $expenseDtlModel->departurePlace = $expenseDtl['departurePlace'];
                $expenseDtlModel->departureTime = Helper::getExpressionTime($expenseDtl['departureTime']);
                $expenseDtlModel->destinationDate = Helper::getExpressionDate($expenseDtl['destinationDate']);
                $expenseDtlModel->destinationPlace = $expenseDtl['destinationPlace'];
                $expenseDtlModel->destinationTime = Helper::getExpressionTime($expenseDtl['destinationTime']);                
                $expenseDtlModel->transportType = $transportType['id'];
                $expenseDtlModel->fare = (float)$expenseDtl['fare'];
                $expenseDtlModel->allowance = ($expenseDtl['allowance']!=null) ? (float)$expenseDtl['allowance'] :null;
                $expenseDtlModel->localConveyence = ($expenseDtl['localConveyence']!=null) ? (float)$expenseDtl['localConveyence'] :null;
                $expenseDtlModel->miscExpenses =($expenseDtl['miscExpense']!=null) ? (float)$expenseDtl['miscExpense'] :null;
                $expenseDtlModel->totalAmount = (float)$expenseDtl['total'];
                $expenseDtlModel->remarks =($expenseDtl['remarks']!=null) ? $expenseDtl['remarks'] :null;
                $expenseDtlModel->status = 'E';
                
                if ($id == 0) {
                    $expenseDtlModel->id = ((int) Helper::getMaxId($this->adapter, TravelExpenseDetail::TABLE_NAME, TravelExpenseDetail::ID)) + 1;
                    $expenseDtlModel->travelId = ($requestedType=='ad') ? $model->travelId :$travelId;
                    $expenseDtlModel->createdBy = $this->employeeId;
                    $expenseDtlModel->createdDate = Helper::getcurrentExpressionDate();
                    $expenseDtlRepo->add($expenseDtlModel);
                }else {
                    $expenseDtlModel->modifiedBy = (int) $this->employeeId;
                    $expenseDtlModel->modifiedDate = Helper::getcurrentExpressionDate();
                    $expenseDtlRepo->edit($expenseDtlModel, $id);
                }
            }
            HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $model, $this->adapter, $this->plugin('url'));
            return new CustomViewModel(['success'=>true,'data'=>['msg'=>'Travel Request Successfully added!!!']]);
        }else{
            $id = (int) $this->params()->fromRoute('id');
            if ($id === 0) {
                return $this->redirect()->toRoute("travelRequest");
            }
            $detail = $this->repository->fetchById($id);
            $travelId = ($detail['REQUESTED_TYPE']=='ep') ? $detail['REFERENCE_TRAVEL_ID'] : $id;
            $referenceDetail = $this->repository->fetchById($travelId);
            return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'advanceAmt'=> $referenceDetail['REQUESTED_AMOUNT'],
                    'detail'=>$referenceDetail,
                    'id'=> $id,
                    'requestedType'=>$detail['REQUESTED_TYPE']
            ]);
        }
    }
    public function deleteExpenseDetailAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $postData = $request->getPost()->getArrayCopy();
            $id = $postData['data']['id'];
            $repository = new TravelExpenseDtlRepository($this->adapter);
            $repository->delete($id);
            $responseData = [
                "success" => true,
                "data" => "Expense Detail Successfully Removed"
            ];  
        }else{
            $responseData = [
                "success" => false,
            ]; 
        }
        return new CustomViewModel($responseData);
    }
    
    public function ExpenseDetailListAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $postData = $request->getPost()->getArrayCopy()['data'];
            $travelId = $postData['travelId'];
            $travelDetail = $this->repository->fetchById($travelId);
            $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
            $expenseDtlList = [];
            $result = $expenseDtlRepo->fetchByTravelId($travelId);
            foreach($result as $row){
                array_push($expenseDtlList, $row);
            }
            return new CustomViewModel([
                'success'=>true,
                'data'=>[
                    'travelDetail'=>$travelDetail,
                    'expenseDtlList'=>$expenseDtlList,
                    'numExpenseDtlList'=>count($expenseDtlList)
                ]
            ]);
        }else {
            return new CustomViewModel(['success'=>false]);
        }
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('travelRequest');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Travel Request Successfully Cancelled!!!");
        return $this->redirect()->toRoute('travelRequest');
    }

    public function viewAction() {
        $this->initializeForm();
        $this->getRecommendApprover();
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("travelRequest");
        }
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };

        $recommenderName = $fullName($this->recommender);
        $approverName = $fullName($this->approver);

        $model = new TravelRequestModel();
        $detail = $this->repository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];
        $recommended_by = $fullName($detail['RECOMMENDED_BY']);
        $approved_by = $fullName($detail['APPROVED_BY']);
        $authRecommender = ($status == 'RQ' || $status == 'C') ? $recommenderName : $recommended_by;
        $authApprover = ($status == 'RC' || $status == 'RQ' || $status == 'C' || ($status == 'R' && $approvedDT == null)) ? $approverName : $approved_by;

        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);

        $employeeName = $fullName($detail['EMPLOYEE_ID']);

        $requestType = array(
            'ad' => 'Advance',
            'ep' => 'Expense'
        );
        if($detail['REFERENCE_TRAVEL_ID']!=null){
            $referenceTravelDtl = $this->repository->fetchById($detail['REFERENCE_TRAVEL_ID']);
            $advanceAmt = $referenceTravelDtl['REQUESTED_AMOUNT'];
        }else{
            $advanceAmt = 0 ;
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'requestTypes' => $requestType,
                    'employeeName' => $employeeName,
                    'status' => $detail['STATUS'],
                    'requestedDate' => $detail['REQUESTED_DATE'],
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'advanceAmt'=>$advanceAmt
        ]);
    }

    public function recommendApproveList() {
        $employeeRepository = new EmployeeRepository($this->adapter);
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $employeeId = $this->employeeId;
        $employeeDetail = $employeeRepository->fetchById($employeeId);
        $branchId = $employeeDetail['BRANCH_ID'];
        $departmentId = $employeeDetail['DEPARTMENT_ID'];
        $designations = $recommendApproveRepository->getDesignationList($employeeId);

        $recommender = array();
        $approver = array();
        foreach ($designations as $key => $designationList) {
            $withinBranch = $designationList['WITHIN_BRANCH'];
            $withinDepartment = $designationList['WITHIN_DEPARTMENT'];
            $designationId = $designationList['DESIGNATION_ID'];
            $employees = $recommendApproveRepository->getEmployeeList($withinBranch, $withinDepartment, $designationId, $branchId, $departmentId);

            if ($key == 1) {
                $i = 0;
                foreach ($employees as $employeeList) {
                    // array_push($recommender,$employeeList);
                    $recommender [$i]["id"] = $employeeList['EMPLOYEE_ID'];
                    $recommender [$i]["name"] = $employeeList['FIRST_NAME'] . " " . $employeeList['MIDDLE_NAME'] . " " . $employeeList['LAST_NAME'];
                    $i++;
                }
            } else if ($key == 2) {
                $i = 0;
                foreach ($employees as $employeeList) {
                    //array_push($approver,$employeeList);
                    $approver [$i]["id"] = $employeeList['EMPLOYEE_ID'];
                    $approver [$i]["name"] = $employeeList['FIRST_NAME'] . " " . $employeeList['MIDDLE_NAME'] . " " . $employeeList['LAST_NAME'];
                    $i++;
                }
            }
        }
        $responseData = [
            "recommender" => $recommender,
            "approver" => $approver
        ];
        return $responseData;
    }

}
