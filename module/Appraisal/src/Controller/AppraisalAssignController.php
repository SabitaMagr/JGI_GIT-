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
        $employeeName = EntityHelper::getTableKVListWithSortOption($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E", "RETIRED_FLAG" => "N"], "FIRST_NAME", "ASC", " ");
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
        $appraisals1 = [-1 => "All"] + $appraisals;
        $appraisalFormElement->setValueOptions($appraisals1);
        $appraisalFormElement->setAttributes(["id" => "appraisalId", "class" => "form-control"]);
        $appraisalFormElement->setLabel("Appraisal");
        
        $request = $this->getRequest();
        if($request->isPost()){
            $postData = $request->getPost();
            switch ($postData->action){
                case "pullEmployeeWidAssignDetail":
                    $responseData = $this->pullEmployeeWidAssignDetail($postData->data);
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

        //$recommApproverRepo = new RecommendApproveRepository($this->adapter);

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeResult = $employeeRepo->filterRecords($employeeId, $branchId, $departmentId, $designationId, -1, -1, -1, 1);

        $employeeList = [];
        foreach ($employeeResult as $employeeRow) {
            $employeeId = $employeeRow['EMPLOYEE_ID'];
//            $recommedApproverList = $recommApproverRepo->getDetailByEmployeeID($employeeId);
//            if ($recommedApproverList != null) {
//                $middleNameR = ($recommedApproverList['MIDDLE_NAME_R'] != null) ? " " . $recommedApproverList['MIDDLE_NAME_R'] . " " : " ";
//                $middleNameA = ($recommedApproverList['MIDDLE_NAME_A'] != null) ? " " . $recommedApproverList['MIDDLE_NAME_A'] . " " : " ";
//                $employeeRow['RECOMMENDER_NAME'] = $recommedApproverList['FIRST_NAME_R'] . $middleNameR . $recommedApproverList['LAST_NAME_R'];
//                $employeeRow['APPROVER_NAME'] = $recommedApproverList['FIRST_NAME_A'] . $middleNameA . $recommedApproverList['LAST_NAME_A'];
//            } else {
//                $employeeRow['RECOMMENDER_NAME'] = "";
//                $employeeRow['APPROVER_NAME'] = "";
//            }
            array_push($employeeList, $employeeRow);
        }
        return [
            "success" => true,
            "data" => $employeeList
        ];
    }
    
}