<?php
namespace Travel\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Travel\Model\RecommenderApprover as RecommenderApproverModel;
use Travel\Repository\RecommenderApproverRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class RecommenderApprover extends AbstractActionController{
    private $adapter;
    private $repository;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new RecommenderApproverRepository($adapter);
    }
    public function indexAction() {
        $request = $this->getRequest();
        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E'], "BRANCH_NAME", "ASC",null,false,true);
        $branches1 = [-1 => "All"] + $branches;
        $branchFormElement->setValueOptions($branches1);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click", "view()");

        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments = EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E'], "DEPARTMENT_NAME", "ASC",null,false,true);
        $departments1 = [-1 => "All"] + $departments;
        $departmentFormElement->setValueOptions($departments1);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E'], "DESIGNATION_TITLE", "ASC",null,false,true);
        $designations1 = [-1 => "All"] + $designations;
        $designationFormElement->setValueOptions($designations1);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");
        
        $employeeResult = EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME,HrEmployees::MIDDLE_NAME,HrEmployees::LAST_NAME], [HrEmployees::STATUS => 'E',HrEmployees::RETIRED_FLAG=>'N'], "FIRST_NAME", "ASC"," ",false,true);
        $employeeList = [];
        foreach($employeeResult as $key=>$value){
            array_push($employeeList, ['id'=>$key,'name'=>$value]);
        }
        $request = $this->getRequest();
        if($request->isPost()){
            $postData = $request->getPost();
            switch ($postData->action){
                case "pullEmployeeWidAssignedDtl":
                    $responseData = $this->pullEmployeeWidAssignedDtl($postData->data);
                    break;
                case "assignTrvlRecommenderApprover":
                    $responseData = $this->assignTrvlRecommenderApprover($postData->data);
                    break;
                default:
                    $responseData = [
                        "success" => false
                    ];
                    break;
            }
            return new CustomViewModel($responseData);
        }
        return Helper::addFlashMessagesToArray($this, [
            "branches" => $branchFormElement,
            "departments" => $departmentFormElement,
            'designations' => $designationFormElement,
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'employeeList'=>$employeeList
        ]);
    }
    public function pullEmployeeWidAssignedDtl($data){
        $companyId = $data['companyId'];
        $positionId = $data['positionId'];
        $serviceTypeId = $data['serviceTypeId'];
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $employeeId = $data['employeeId'];
        $serviceEventTypeId = (!isset($data['serviceEventTypeId']) || $data['serviceEventTypeId'] == null) ? -1 : $data['serviceEventTypeId'];
        $recommenderId = (!isset($data['recommenderId']) || $data['recommenderId'] == null) ? -1 : $data['recommenderId'];
        $approverId = (!isset($data['approverId']) || $data['approverId'] == null) ? -1 : $data['approverId'];

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeResult = $employeeRepo->filterRecords($employeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, 1, $companyId);

        $employeeList = [];
        foreach ($employeeResult as $employeeRow) {
            $employeeId = $employeeRow['EMPLOYEE_ID'];
            $recommedApproverList = $this->repository->getDetailByEmployeeID($employeeId, $recommenderId, $approverId);
            if ($recommedApproverList != null) {
                $middleNameR = ($recommedApproverList['MIDDLE_NAME_R'] != null) ? " " . $recommedApproverList['MIDDLE_NAME_R'] . " " : " ";
                $middleNameA = ($recommedApproverList['MIDDLE_NAME_A'] != null) ? " " . $recommedApproverList['MIDDLE_NAME_A'] . " " : " ";

                if ($recommedApproverList['RETIRED_R'] != 'Y' && $recommedApproverList['STATUS_R'] != 'D') {
                    $employeeRow['RECOMMENDER_NAME'] = $recommedApproverList['FIRST_NAME_R'] . $middleNameR . $recommedApproverList['LAST_NAME_R'];
                    $employeeRow['RETIRED_R'] = $recommedApproverList['RETIRED_R'];
                    $employeeRow['STATUS_R'] = $recommedApproverList['STATUS_R'];
                    $employeeRow['RECOMMENDER_ID'] = $recommedApproverList['RECOMMEND_BY'];
                } else {
                    $employeeRow['RECOMMENDER_NAME'] = "";
                    $employeeRow['RETIRED_R'] = "";
                    $employeeRow['STATUS_R'] = "";
                    $employeeRow['RECOMMENDER_ID'] = null;
                }
                if ($recommedApproverList['RETIRED_A'] != 'Y' && $recommedApproverList['STATUS_A'] != 'D') {
                    $employeeRow['APPROVER_NAME'] = $recommedApproverList['FIRST_NAME_A'] . $middleNameA . $recommedApproverList['LAST_NAME_A'];
                    $employeeRow['RETIRED_A'] = $recommedApproverList['RETIRED_A'];
                    $employeeRow['STATUS_A'] = $recommedApproverList['STATUS_A'];
                    $employeeRow['APPROVER_ID'] = $recommedApproverList['APPROVED_BY'];
                } else {
                    $employeeRow['APPROVER_NAME'] = "";
                    $employeeRow['RETIRED_A'] = "";
                    $employeeRow['STATUS_A'] = "";
                    $employeeRow['APPROVER_ID'] = null;
                }
            } else {
                $employeeRow['RECOMMENDER_NAME'] = "";
                $employeeRow['RETIRED_R'] = "";
                $employeeRow['STATUS_R'] = "";
                $employeeRow['RECOMMENDER_ID'] = null;

                $employeeRow['APPROVER_NAME'] = "";
                $employeeRow['RETIRED_A'] = "";
                $employeeRow['STATUS_A'] = "";
                $employeeRow['APPROVER_ID'] = null;
            }
            array_push($employeeList, $employeeRow);
        }
        ///  print_r($employeeList); die();
        return [
            "success" => true,
            "data" => $employeeList
        ];
    }
    public function assignTrvlRecommenderApprover($data){
        $employeeId = $data['employeeId'];
        $recommenderId = $data['recommenderId'];
        $approverId = $data['approverId'];

        if ($recommenderId == "" || $recommenderId == null) {
            $recommenderIdNew = null;
        } else if ($employeeId == $recommenderId) {
            $recommenderIdNew = "";
        } else {
            $recommenderIdNew = $recommenderId;
        }

        if ($approverId == "" || $approverId == null) {
            $approverIdNew = null;
        } else if ($employeeId == $approverId) {
            $approverIdNew = "";
        } else {
            $approverIdNew = $approverId;
        }
        $recommendApprove = new RecommenderApproverModel();
        $employeePreDtl = $this->repository->fetchById($employeeId);
        if ($employeePreDtl == null) {
            $recommendApprove->employeeId = $employeeId;
            $recommendApprove->recommendBy = $recommenderIdNew;
            $recommendApprove->approvedBy = $approverIdNew;
            $recommendApprove->createdDt = Helper::getcurrentExpressionDate();
            $recommendApprove->status = 'E';
            $this->repository->add($recommendApprove);
        } else if ($employeePreDtl != null) {
            $id = $employeePreDtl['EMPLOYEE_ID'];
            $recommendApprove->employeeId = $employeeId;
            $recommendApprove->recommendBy = $recommenderIdNew;
            $recommendApprove->approvedBy = $approverIdNew;
            $recommendApprove->modifiedDt = Helper::getcurrentExpressionDate();
            $recommendApprove->status = 'E';
            $this->repository->edit($recommendApprove, $id);
        }
        return [
            "success" => true,
            "data" => $data
        ];
    }
}