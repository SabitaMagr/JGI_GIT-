<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/3/16
 * Time: 3:26 PM
 */
namespace LeaveManagement\Controller;

use Application\Helper\Helper;
use LeaveManagement\Repository\LeaveBalanceRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Setup\Model\ServiceEventType;
use Zend\Form\Element\Select;
use Zend\Form\Annotation\AnnotationBuilder;
use LeaveManagement\Form\LeaveApplyForm;
use LeaveManagement\Model\LeaveApply;
use Setup\Repository\RecommendApproveRepository;
use Setup\Repository\EmployeeRepository;
use SelfService\Repository\LeaveRequestRepository;
use LeaveManagement\Repository\LeaveMasterRepository;

class LeaveBalance extends AbstractActionController {
    private $adapter;
    private $repository;
    private $form;
    private $leaveRequestRepository;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new LeaveBalanceRepository($adapter);
        $this->leaveRequestRepository = new LeaveRequestRepository($adapter);
    }
    public function initializeForm(){
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction()
    {
        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
        $employeeName=\Application\Helper\EntityHelper::getTableKVList($this->adapter,"HR_EMPLOYEES","EMPLOYEE_ID",["FIRST_NAME","MIDDLE_NAME","LAST_NAME"],["STATUS"=>"E"]);
        $employeeName[-1]="All";
        ksort($employeeName);
        $employeeNameFormElement->setValueOptions($employeeName);
        $employeeNameFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeNameFormElement->setLabel("Employee");
        $employeeNameFormElement->setAttribute("ng-click","view()");

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches=\Application\Helper\EntityHelper::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME],[Branch::STATUS=>'E']);
        $branches[-1]="All";
        ksort($branches);
        $branchFormElement->setValueOptions($branches);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click","view()");


        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments=\Application\Helper\EntityHelper::getTableKVList($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME],[Department::STATUS=>'E']);
        $departments[-1]="All";
        ksort($departments);
        $departmentFormElement->setValueOptions($departments);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations=\Application\Helper\EntityHelper::getTableKVList($this->adapter,Designation::TABLE_NAME,Designation::DESIGNATION_ID , [Designation::DESIGNATION_TITLE],[Designation::STATUS=>'E']);
        $designations[-1]="All";
        ksort($designations);
        $designationFormElement->setValueOptions($designations);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");

        $positionFormElement = new Select();
        $positionFormElement->setName("position");
        $positions=\Application\Helper\EntityHelper::getTableKVList($this->adapter,Position::TABLE_NAME,Position::POSITION_ID , [Position::POSITION_NAME],[Position::STATUS=>'E']);
        $positions[-1]="All";
        ksort($positions);
        $positionFormElement->setValueOptions($positions);
        $positionFormElement->setAttributes(["id" => "positionId", "class" => "form-control"]);
        $positionFormElement->setLabel("Position");

        $serviceTypeFormElement = new Select();
        $serviceTypeFormElement->setName("serviceType");
        $serviceTypes=\Application\Helper\EntityHelper::getTableKVList($this->adapter,ServiceType::TABLE_NAME,ServiceType::SERVICE_TYPE_ID , [ServiceType::SERVICE_TYPE_NAME],[ServiceType::STATUS=>'E']);
        $serviceTypes[-1]="All";
        ksort($serviceTypes);
        $serviceTypeFormElement->setValueOptions($serviceTypes);
        $serviceTypeFormElement->setAttributes(["id" => "serviceTypeId", "class" => "form-control"]);
        $serviceTypeFormElement->setLabel("Service Type");
        
        $serviceEventTypeFormElement = new Select();
        $serviceEventTypeFormElement->setName("serviceEventType");
        $serviceEventTypes=\Application\Helper\EntityHelper::getTableKVList($this->adapter,ServiceEventType::TABLE_NAME,ServiceEventType::SERVICE_EVENT_TYPE_ID , [ServiceEventType::SERVICE_EVENT_TYPE_NAME],[ServiceEventType::STATUS=>'E']);
        $serviceEventTypes[-1]="All";
        ksort($serviceEventTypes);
        $serviceEventTypeFormElement->setValueOptions($serviceEventTypes);
        $serviceEventTypeFormElement->setAttributes(["id" => "serviceEventTypeId", "class" => "form-control"]);
        $serviceEventTypeFormElement->setLabel("Service Event Type");

        $leaveList = $this->repository->getAllLeave();
        $num = count($leaveList);

        return Helper::addFlashMessagesToArray($this,[
            'leaveList'=>$leaveList,
            'num'=>$num,
            "branches"=>$branchFormElement,
            "departments"=>$departmentFormElement,
            'designations'=>$designationFormElement,
            'positions'=>$positionFormElement,
            'serviceTypes'=>$serviceTypeFormElement,
            'serviceEventTypes'=>$serviceEventTypeFormElement,
            'employees'=>$employeeNameFormElement
        ]);
    }
    public function applyAction(){
        $leaveId = $this->params()->fromRoute('id');
        $employeeId = $this->params()->fromRoute('eid');

        $this->initializeForm();
        $request = $this->getRequest();

        $recommendApproveRepository =  new RecommendApproveRepository($this->adapter);
        $empRecommendApprove  = $recommendApproveRepository->fetchById($employeeId);

        $leaveBalanceDtl = $this->repository->getByEmpIdLeaveId($employeeId,$leaveId);
        $leaveRepository = new LeaveMasterRepository($this->adapter);
        $leaveDtl  = $leaveRepository->fetchById($leaveId);
        
        $employeeRepository =  new EmployeeRepository($this->adapter);
        $employeeDtl = $employeeRepository->fetchById($employeeId);

        if($empRecommendApprove!=null){
            $recommendBy = $empRecommendApprove['RECOMMEND_BY'];
            $approvedBy = $empRecommendApprove['APPROVED_BY'];
        }else{
            $result = $this->recommendApproveList($employeeId);
            if(count($result['recommender'])>0){
                $recommendBy=$result['recommender'][0]['id'];
            }else{
                $recommendBy=null;
            }
            if(count($result['approver'])>0){
                $approvedBy=$result['approver'][0]['id'];
            }else{
                $approvedBy=null;
            }           
        }
        
        if($request->isPost()){
            $this->form->setData($request->getPost());

            if($this->form->isValid()){
                $leaveRequest = new LeaveApply();
                $leaveRequest->exchangeArrayFromForm($this->form->getData());

                $leaveRequest->id = (int) Helper::getMaxId($this->adapter, LeaveApply::TABLE_NAME, LeaveApply::ID)+1;
                $leaveRequest->employeeId=$employeeId;
                $leaveRequest->leaveId=$leaveId;
                $leaveRequest->startDate=Helper::getExpressionDate($leaveRequest->startDate);
                $leaveRequest->endDate=Helper::getExpressionDate($leaveRequest->endDate);
                $leaveRequest->recommendedBy=$recommendBy;
                $leaveRequest->approvedBy=$approvedBy;
                $leaveRequest->requestedDt = Helper::getcurrentExpressionDate();
                $leaveRequest->status = "RQ";

                $this->leaveRequestRepository->add($leaveRequest);
                $this->flashmessenger()->addMessage("Leave Request Successfully added!!!");
                return $this->redirect()->toRoute("leavebalance");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'employeeId'=>$employeeId,
            'leaveId'=>$leaveId,
            'employeeName'=>$employeeDtl['FIRST_NAME']." ".$employeeDtl['MIDDLE_NAME']." ".$employeeDtl['LAST_NAME'],
            'leaveName'=>$leaveDtl['LEAVE_ENAME'],
            'balance'=>$leaveBalanceDtl['BALANCE'],
            'allowHalfDay'=>$leaveDtl['ALLOW_HALFDAY'],
            'customRenderer'=>Helper::renderCustomView()
        ]);
    }
    public function recommendApproveList($employeeId){
        $employeeRepository = new EmployeeRepository($this->adapter);
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $employeeId = $employeeId;
        $employeeDetail = $employeeRepository->fetchById($employeeId);
        $branchId = $employeeDetail['BRANCH_ID'];
        $departmentId = $employeeDetail['DEPARTMENT_ID'];
        $designations =$recommendApproveRepository->getDesignationList($employeeId);

        $recommender = array();
        $approver = array();
        foreach($designations as $key=>$designationList){
            $withinBranch = $designationList['WITHIN_BRANCH'];
            $withinDepartment = $designationList['WITHIN_DEPARTMENT'];
            $designationId = $designationList['DESIGNATION_ID'];
            $employees = $recommendApproveRepository->getEmployeeList($withinBranch,$withinDepartment,$designationId,$branchId,$departmentId);

            if($key==1){
                $i=0;
                foreach($employees as $employeeList){
                    // array_push($recommender,$employeeList);
                    $recommender [$i]["id"]=$employeeList['EMPLOYEE_ID'];
                    $recommender [$i]["name"]= $employeeList['FIRST_NAME']." ".$employeeList['MIDDLE_NAME']." ".$employeeList['LAST_NAME'];
                    $i++;
                }
            }else if($key==2){
                $i=0;
                foreach($employees as $employeeList){
                    //array_push($approver,$employeeList);
                    $approver [$i]["id"]=$employeeList['EMPLOYEE_ID'];
                    $approver [$i]["name"]= $employeeList['FIRST_NAME']." ".$employeeList['MIDDLE_NAME']." ".$employeeList['LAST_NAME'];
                    $i++;
                }
            }
        }
        $responseData = [
            "recommender" => $recommender,
            "approver"=>$approver
        ];
        return $responseData;
    }
}