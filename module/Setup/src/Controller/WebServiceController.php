<?php
namespace Setup\Controller;

use Setup\Helper\EntityHelper;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class WebServiceController extends AbstractActionController {

    private $adapter;
    private $loggedInEmployeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->loggedInEmployeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $request = $this->getRequest();
        $responseData = [];
        if ($request->isPost()) {
            $postedData = $request->getPost();
            switch ($postedData->action) {

                case "pullRecommendApproveList":
                    $employeeRepository = new EmployeeRepository($this->adapter);
                    $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
                    $employeeId = $postedData->employeeId;
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
                    if (count($recommender) == 0) {
                        $recommender[0]["id"] = " ";
                        $recommender[0]["name"] = "--";
                    }
                    if (count($approver) == 0) {
                        $approver[0]["id"] = " ";
                        $approver[0]["name"] = "--";
                    }
                    $responseData = [
                        "success" => true,
                        "recommender" => $recommender,
                        "approver" => $approver
                    ];
                    break;

                case "pullEmpRecommendApproveDtl":
                    $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
                    $employeeId = $postedData->employeeId;
                    $result = $recommendApproveRepository->fetchById($employeeId);
                    $responseData = [
                        "success" => true,
                        "data" => $result
                    ];
                    break;
                default:
                    $responseData = [
                        "success" => false
                    ];
                    break;
            }
        } else {
            $responseData = [
                "success" => false
            ];
        }
        return new JsonModel($responseData);
    }

    

   
}
