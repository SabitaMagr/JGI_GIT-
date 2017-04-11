<?php
namespace Training\Controller;

use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Training\Repository\TrainingStatusRepository;
use ManagerService\Repository\TrainingApproveRepository;
use SelfService\Repository\TrainingRequestRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Form\TrainingRequestForm;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Model\TrainingRequest;
use Setup\Repository\TrainingRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Form\Element\Select;
use Setup\Model\ServiceEventType;
use Zend\Authentication\AuthenticationService;
use Setup\Repository\RecommendApproveRepository;

class TrainingStatusController extends AbstractActionController
{
    private $adapter;
    private $trainingApproveRepository;
    private $trainingStatusRepository;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->trainingApproveRepository = new TrainingApproveRepository($adapter);
        $this->trainingStatusRepository = new TrainingStatusRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId =  $auth->getStorage()->read()['employee_id'];       
    }
    
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new TrainingRequestForm();
        $this->form = $builder->createForm($form);
    }

    
    public function indexAction() {
        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
        $employeeName = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ");
        $employeeName1 = [-1 => "All"] + $employeeName;
        $employeeNameFormElement->setValueOptions($employeeName1);
        $employeeNameFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeNameFormElement->setLabel("Employee");
        $employeeNameFormElement->setAttribute("ng-click", "view()");

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E'], "BRANCH_NAME", "ASC");
        $branches1 = [-1 => "All"] + $branches;
        $branchFormElement->setValueOptions($branches1);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click", "view()");

        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E'], "DEPARTMENT_NAME", "ASC");
        $departments1 = [-1 => "All"] + $departments;
        $departmentFormElement->setValueOptions($departments1);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E'], "DESIGNATION_TITLE", "ASC");
        $designations1 = [-1 => "All"] + $designations;
        $designationFormElement->setValueOptions($designations1);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");

        $positionFormElement = new Select();
        $positionFormElement->setName("position");
        $positions = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME], [Position::STATUS => 'E'], "POSITION_NAME", "ASC");
        $positions1 = [-1 => "All"] + $positions;
        $positionFormElement->setValueOptions($positions1);
        $positionFormElement->setAttributes(["id" => "positionId", "class" => "form-control"]);
        $positionFormElement->setLabel("Position");

        $serviceTypeFormElement = new Select();
        $serviceTypeFormElement->setName("serviceType");
        $serviceTypes = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => 'E'], "SERVICE_TYPE_NAME", "ASC");
        $serviceTypes1 = [-1 => "All"] + $serviceTypes;
        $serviceTypeFormElement->setValueOptions($serviceTypes1);
        $serviceTypeFormElement->setAttributes(["id" => "serviceTypeId", "class" => "form-control"]);
        $serviceTypeFormElement->setLabel("Service Type");
        
        $serviceEventTypeFormElement = new Select();
        $serviceEventTypeFormElement->setName("serviceEventType");
        $serviceEventTypes = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => 'E'], "SERVICE_EVENT_TYPE_NAME", "ASC");
        $serviceEventTypes1 = [-1 => "Working"] + $serviceEventTypes;
        $serviceEventTypeFormElement->setValueOptions($serviceEventTypes1);
        $serviceEventTypeFormElement->setAttributes(["id" => "serviceEventTypeId", "class" => "form-control"]);
        $serviceEventTypeFormElement->setLabel("Service Event Type");

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
                    "branches" => $branchFormElement,
                    "departments" => $departmentFormElement,
                    'designations' => $designationFormElement,
                    'positions' => $positionFormElement,
                    'serviceTypes' => $serviceTypeFormElement,
                    'employees' => $employeeNameFormElement,
                    'status' => $statusFormElement,
                    'serviceEventTypes' => $serviceEventTypeFormElement
        ]);
    }
    public function viewAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("trainingStatus");
        }
        $trainingRequestModel = new TrainingRequest();
        $request = $this->getRequest();

        $detail = $this->trainingApproveRepository->fetchById($id);
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

        if($detail['TRAINING_ID']!=0){
            $detail['START_DATE']=$detail['T_START_DATE'];
            $detail['END_DATE'] = $detail['T_END_DATE'];
            $detail['DURATION'] = $detail['T_DURATION'];
            $detail['TRAINING_TYPE'] = $detail['T_TRAINING_TYPE'];
        }
        if (!$request->isPost()) {
            $trainingRequestModel->exchangeArrayFromDB($detail);
            $this->form->bind($trainingRequestModel);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $trainingRequestModel->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $trainingRequestModel->status = "R";
                $this->flashmessenger()->addMessage("Training Request Rejected!!!");
            } else if ($action == "Approve") {
                $trainingRequestModel->status = "AP";
                $this->flashmessenger()->addMessage("Training Request Approved");
            }
            $trainingRequestModel->approvedBy = $this->employeeId;
            $trainingRequestModel->approvedRemarks = $reason;
            $this->trainingApproveRepository->edit($trainingRequestModel, $id);

            return $this->redirect()->toRoute("trainingStatus");
        }
        $trainingTypes = array(
           'CP'=>'Company Personal',
           'CC'=>'Company Contribution'
        );
        $trainings = $this->getTrainingList($requestedEmployeeID);
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
                    'holidays' => $this->getHolidayList($requestedEmployeeID),
                    'customRenderer' => Helper::renderCustomView(),
                    'recommApprove'=>$recommApprove,
                    'trainingIdSelected'=>$detail['TRAINING_ID'],
                    'trainings' => $trainings["trainingKVList"],
                    'trainingTypes'=>$trainingTypes,
        ]);
    }
    public function getTrainingList($employeeId) {
        $trainingRepo = new TrainingRepository($this->adapter);
        $trainingResult = $trainingRepo->selectAll($employeeId);
        $trainingList = [];
        $allTrainings = [];
        foreach ($trainingResult as $trainingRow) {
            $trainingList[$trainingRow['TRAINING_ID']] = $trainingRow['TRAINING_NAME'] . " (" . $trainingRow['START_DATE'] . " to " . $trainingRow['END_DATE'] . ")";
            $allTrainings[$trainingRow['TRAINING_ID']] = $trainingRow;
        }
        return ['trainingKVList' => $trainingList,'trainingList'=>$allTrainings];
    }   
}