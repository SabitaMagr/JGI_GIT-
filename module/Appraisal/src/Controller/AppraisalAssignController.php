<?php
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Appraisal\Repository\AppraisalAssignRepository;
use Zend\Authentication\AuthenticationService;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\HrEmployees;
use Appraisal\Model\Setup;
use Zend\Form\Element\Select;
use Application\Custom\CustomViewModel;
use Setup\Repository\EmployeeRepository;
use Appraisal\Model\AppraisalAssign;

class AppraisalAssignController extends AbstractActionController{
    private $adapter;
    private $repository;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->repository = new AppraisalAssignRepository($adapter);
        $this->adapter = $adapter;
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
    }
    
    public function indexAction() {
        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
        $employeeName = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E", "RETIRED_FLAG" => "N"], "FIRST_NAME", "ASC", " ");
        $employeeName1 = [-1 => "All"] + $employeeName;
        $employeeNameFormElement->setValueOptions($employeeName1);
        $employeeNameFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeNameFormElement->setLabel("Employee");
        $employeeNameFormElement->setAttribute("ng-click", "view()");

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E'], "BRANCH_NAME", "ASC");
        $branches1 = [-1 => "All"] + $branches;
        $branchFormElement->setValueOptions($branches1);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click", "view()");

        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments = EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E'], "DEPARTMENT_NAME", "ASC");
        $departments1 = [-1 => "All"] + $departments;
        $departmentFormElement->setValueOptions($departments1);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E'], "DESIGNATION_TITLE", "ASC");
        $designations1 = [-1 => "All"] + $designations;
        $designationFormElement->setValueOptions($designations1);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");
        
        $appraisalFormElement = new Select();
        $appraisalFormElement->setName("appraisal");
        $appraisals = EntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::APPRAISAL_ID, [Setup::APPRAISAL_EDESC], [Setup::STATUS => 'E'], Setup::APPRAISAL_EDESC, "ASC");
        $appraisalFormElement->setValueOptions($appraisals);
        $appraisalFormElement->setAttributes(["id" => "appraisalId", "class" => "form-control"]);
        $appraisalFormElement->setLabel("Appraisal");
        
        $request = $this->getRequest();
        if($request->isPost()){
            $postData = $request->getPost();
            switch ($postData->action){
                case "pullEmployeeWidAssignDetail":
                    $responseData = $this->pullEmployeeWidAssignDetail($postData->data);
                    break;
                case "pullEmployeeListForReportingRole":
                    $responseData = $this->pullEmployeeListForReportingRole($postData->data);
                    break;
                case "assignAppraisal":
                    $responseData = $this->assignAppraisal($postData->data);
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
            'employees'=>$employeeNameFormElement,
            'branches'=>$branchFormElement,
            'departments'=>$departmentFormElement,
            'designations'=>$designationFormElement,
            'appraisals'=>$appraisalFormElement            
        ]);
    }
    
    public function pullEmployeeWidAssignDetail($data){
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];
        $employeeId = $data['employeeId'];
        $appraisalId = $data['appraisalId'];

        
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeResult = $employeeRepo->filterRecords($employeeId, $branchId, $departmentId, $designationId, -1, -1, -1, 1);

        $employeeList = [];
        foreach ($employeeResult as $employeeRow) {
            $employeeId = $employeeRow['EMPLOYEE_ID'];
            $assignList = $this->repository->getDetailByEmpAppraisalId($employeeId,$appraisalId);
            
            if ($assignList != null) {
                $middleNameR = ($assignList['MIDDLE_NAME_R'] != null) ? " " . $assignList['MIDDLE_NAME_R'] . " " : " ";
                $middleNameA = ($assignList['MIDDLE_NAME_A'] != null) ? " " . $assignList['MIDDLE_NAME_A'] . " " : " ";
                
                if($assignList['RETIRED_R']!='Y' && $assignList['STATUS_R']!='D'){
                    $employeeRow['REVIEWER_NAME'] = $assignList['FIRST_NAME_R'] . $middleNameR . $assignList['LAST_NAME_R'];
                }else{
                    $employeeRow['REVIEWER_NAME'] = "";
                }
                if($assignList['RETIRED_A']!='Y' && $assignList['STATUS_A']!='D'){
                    $employeeRow['APPRAISER_NAME'] = $assignList['FIRST_NAME_A'] . $middleNameA . $assignList['LAST_NAME_A'];
                }else{
                    $employeeRow['APPRAISER_NAME'] = "";
                }
                $employeeRow['APPRAISAL_EDESC'] = $assignList['APPRAISAL_EDESC'];
            } else {
                $employeeRow['REVIEWER_NAME'] = "";
                $employeeRow['APPRAISER_NAME'] = "";
                $employeeRow['APPRAISAL_EDESC'] = "";
            }
            
            array_push($employeeList, $employeeRow);
        }
//        print "<pre>";
//        print_r($employeeList); die();
        return [
            "success" => true,
            "data" => $employeeList
        ];
    }
    
    public function pullEmployeeListForReportingRole($data){
        $branchId = $data['branchId'];
        $departmentId = $data['departmentId'];
        $designationId = $data['designationId'];

        $repository = new EmployeeRepository($this->adapter);
        $employeeResult = $repository->filterRecords(-1, $branchId, $departmentId, $designationId, -1, -1, -1, 1);

        $employeeList = [];
        $i = 0;
        foreach ($employeeResult as $employeeRow) {
            if ($employeeRow['MIDDLE_NAME'] != null) {
                $middleName = " " . $employeeRow['MIDDLE_NAME'] . " ";
            } else {
                $middleName = " ";
            }
            $employeeList [$i]["id"] = $employeeRow['EMPLOYEE_ID'];
            $employeeList [$i]["name"] = $employeeRow['FIRST_NAME'] . $middleName . $employeeRow['LAST_NAME'];
            $i++;
        }
        return [
            'success' => true,
            'data' => $employeeList
        ];
    }
    public function assignAppraisal($data){
        $employeeId = (int)$data['employeeId'];
        $reviewerId = (int)$data['reviewerId'];
        $appraiserId = (int)$data['appraiserId'];
        $appraisalId = (int)$data['appraisalId'];
//        print_r($appraisalId); die();
        
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        
        
        if ($reviewerId == "" || $reviewerId == null) {
            $reviewerIdNew = null;
        } else if ($employeeId == $reviewerId) {
            $reviewerIdNew = "";
        } else {
            $reviewerIdNew = $reviewerId;
        }

        if ($appraiserId == "" || $appraiserId == null) {
            $appraiserIdNew = null;
        } else if ($employeeId == $appraiserId) {
            $appraiserIdNew = "";
        } else {
            $appraiserIdNew = $appraiserId;
        }

        $appraisalAssign = new AppraisalAssign();
        $employeePreDtl = $this->repository->getDetailByEmpAppraisalId($employeeId,$appraisalId);
        if ($employeePreDtl == null) {
            $appraisalAssign->employeeId = $employeeId;
            $appraisalAssign->appraisalId = $appraisalId;
            $appraisalAssign->reviewerId = $reviewerIdNew;
            $appraisalAssign->appraiserId = $appraiserIdNew;
            $appraisalAssign->createdDate = Helper::getcurrentExpressionDate();
            $appraisalAssign->approvedDate = Helper::getcurrentExpressionDate();
            $appraisalAssign->createdBy = $this->employeeId;
            $appraisalAssign->companyId = $employeeDetail['COMPANY_ID'];
            $appraisalAssign->branchId = $employeeDetail['BRANCH_ID'];
            $appraisalAssign->status = 'E';
            $this->repository->add($appraisalAssign);
        } else if ($employeePreDtl != null) {
            $id = $employeePreDtl['EMPLOYEE_ID'];
            $appraisalAssign->employeeId = $employeeId;
            $appraisalAssign->reviewerId = $reviewerIdNew;
            $appraisalAssign->appraiserId = $appraiserIdNew;
            $appraisalAssign->modifiedDate = Helper::getcurrentExpressionDate();
            $appraisalAssign->modifiedBy = $this->employeeId;
            $appraisalAssign->status = 'E';
            $this->repository->edit($appraisalAssign, [$employeeId,$appraisalId]);
        }
        return [
            "success" => true,
            "data" => $data
        ];
    }
    
}