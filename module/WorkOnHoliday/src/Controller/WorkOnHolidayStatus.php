<?php
namespace WorkOnHoliday\Controller;

use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use WorkOnHoliday\Repository\WorkOnHolidayStatusRepository;
use ManagerService\Repository\HolidayWorkApproveRepository;
use SelfService\Repository\WorkOnHolidayRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Form\WorkOnHolidayForm;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Model\WorkOnHoliday;
use SelfService\Repository\HolidayRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Form\Element\Select;
use Setup\Model\ServiceEventType;
use HolidayManagement\Model\Holiday;
use Zend\Authentication\AuthenticationService;
use Setup\Repository\RecommendApproveRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use LeaveManagement\Repository\LeaveAssignRepository;

class WorkOnHolidayStatus extends AbstractActionController
{
    private $adapter;
    private $holidayWorkApproveRepository;
    private $workOnHolidayStatusRepository;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->holidayWorkApproveRepository = new HolidayWorkApproveRepository($adapter);
        $this->workOnHolidayStatusRepository = new WorkOnHolidayStatusRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId =  $auth->getStorage()->read()['employee_id'];       
    }
    
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new WorkOnHolidayForm();
        $this->form = $builder->createForm($form);
    }

    
    public function indexAction() {
        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
        $employeeName = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ",false,true);
        $employeeName1 = [-1 => "All"] + $employeeName;
        $employeeNameFormElement->setValueOptions($employeeName1);
        $employeeNameFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeNameFormElement->setLabel("Employee");
        $employeeNameFormElement->setAttribute("ng-click", "view()");

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E'], "BRANCH_NAME", "ASC",null,false,true);
        $branches1 = [-1 => "All"] + $branches;
        $branchFormElement->setValueOptions($branches1);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click", "view()");

        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E'], "DEPARTMENT_NAME", "ASC",null,false,true);
        $departments1 = [-1 => "All"] + $departments;
        $departmentFormElement->setValueOptions($departments1);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E'], "DESIGNATION_TITLE", "ASC",null,false,true);
        $designations1 = [-1 => "All"] + $designations;
        $designationFormElement->setValueOptions($designations1);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");

        $positionFormElement = new Select();
        $positionFormElement->setName("position");
        $positions = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME], [Position::STATUS => 'E'], "POSITION_NAME", "ASC",null,false,true);
        $positions1 = [-1 => "All"] + $positions;
        $positionFormElement->setValueOptions($positions1);
        $positionFormElement->setAttributes(["id" => "positionId", "class" => "form-control"]);
        $positionFormElement->setLabel("Position");

        $serviceTypeFormElement = new Select();
        $serviceTypeFormElement->setName("serviceType");
        $serviceTypes = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => 'E'], "SERVICE_TYPE_NAME", "ASC",null,false,true);
        $serviceTypes1 = [-1 => "All"] + $serviceTypes;
        $serviceTypeFormElement->setValueOptions($serviceTypes1);
        $serviceTypeFormElement->setAttributes(["id" => "serviceTypeId", "class" => "form-control"]);
        $serviceTypeFormElement->setLabel("Service Type");
        
        $serviceEventTypeFormElement = new Select();
        $serviceEventTypeFormElement->setName("serviceEventType");
        $serviceEventTypes = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => 'E'], "SERVICE_EVENT_TYPE_NAME", "ASC",null,false,true);
        $serviceEventTypes1 = [-1 => "Working"] + $serviceEventTypes;
        $serviceEventTypeFormElement->setValueOptions($serviceEventTypes1);
        $serviceEventTypeFormElement->setAttributes(["id" => "serviceEventTypeId", "class" => "form-control"]);
        $serviceEventTypeFormElement->setLabel("Service Event Type");

        $holidayFormElement = new Select();
        $holidayFormElement->setName("holiday");
        $holidays = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME], [Holiday::STATUS => 'E'],Holiday::HOLIDAY_ENAME,"ASC",null,false,true);
        $holidays1 = [-1 => "All"] + $holidays;
        $holidayFormElement->setValueOptions($holidays1);
        $holidayFormElement->setAttributes(["id" => "holidayId", "class" => "form-control"]);
        $holidayFormElement->setLabel("Holiday Type");

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
                    'holidays' => $holidayFormElement,
                    'employees' => $employeeNameFormElement,
                    'status' => $statusFormElement,
                    'serviceEventTypes' => $serviceEventTypeFormElement
        ]);
    }
    public function viewAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("workOnHolidayStatus");
        }
        $workOnHolidayModel = new WorkOnHoliday();
        $request = $this->getRequest();

        $detail = $this->holidayWorkApproveRepository->fetchById($id);
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
            $workOnHolidayModel->exchangeArrayFromDB($detail);
            $this->form->bind($workOnHolidayModel);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $workOnHolidayModel->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $workOnHolidayModel->status = "R";
                $this->flashmessenger()->addMessage("Work on Holiday Request Rejected!!!");
            } else if ($action == "Approve") {
                $leaveMasterRepo = new LeaveMasterRepository($this->adapter);
                $leaveAssignRepo = new LeaveAssignRepository($this->adapter);
                $substituteLeave = $leaveMasterRepo->getSubstituteLeave()->getArrayCopy();
                $substituteLeaveId = $substituteLeave['LEAVE_ID'];
                $empSubLeaveDtl = $leaveAssignRepo->filterByLeaveEmployeeId($substituteLeaveId, $requestedEmployeeID);
                if(count($empSubLeaveDtl)>0){
                    $preBalance = $empSubLeaveDtl['BALANCE'];
                    $total = $empSubLeaveDtl['TOTAL_DAYS'] + $detail['DURATION'];
                    $balance = $preBalance + $detail['DURATION'];
                    $leaveAssignRepo->updatePreYrBalance($requestedEmployeeID,$substituteLeaveId, 0,$total, $balance);
                }else{
                    $leaveAssign = new \LeaveManagement\Model\LeaveAssign();
                    $leaveAssign->createdDt = Helper::getcurrentExpressionDate();
                    $leaveAssign->createdBy = $this->employeeId;
                    $leaveAssign->employeeId = $requestedEmployeeID;
                    $leaveAssign->leaveId = $substituteLeaveId;
                    $leaveAssign->totalDays = $detail['DURATION'];
                    $leaveAssign->previousYearBalance = 0;
                    $leaveAssign->balance = $detail['DURATION'];
                    $leaveAssignRepo->add($leaveAssign);
                }
                $workOnHolidayModel->status = "AP";
                $this->flashmessenger()->addMessage("Work on Holiday Request Approved");
            }
            $workOnHolidayModel->approvedBy = $this->employeeId;
            $workOnHolidayModel->approvedRemarks = $reason;
            $this->holidayWorkApproveRepository->edit($workOnHolidayModel, $id);

            return $this->redirect()->toRoute("workOnHolidayStatus");
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
                    'holidays' => $this->getHolidayList($requestedEmployeeID),
                    'customRenderer' => Helper::renderCustomView(),
                    'recommApprove'=>$recommApprove
        ]);
    }
    public function getHolidayList($employeeId){
       $holidayRepo = new HolidayRepository($this->adapter);
       $holidayResult = $holidayRepo->selectAll($employeeId);
       $holidayList = [];
       foreach($holidayResult as $holidayRow){
           //$todayDate = new \DateTime();
           $holidayList[$holidayRow['HOLIDAY_ID']]=$holidayRow['HOLIDAY_ENAME']." (".$holidayRow['START_DATE']." to ".$holidayRow['END_DATE'].")";
       }
       return $holidayList;
    }    
}