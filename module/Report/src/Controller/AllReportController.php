<?php

namespace Report\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Report\Repository\ReportRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class AllReportController extends AbstractActionController {

    private $adapter;
    private $reportRepo;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->reportRepo = new ReportRepository($this->adapter);
    }

    public function indexAction() {
        
    }

    public function departmentAllAction() {
        
    }

    public function departmentWiseAction() {
        $departmentId = (int) $this->params()->fromRoute('id1');
        return Helper::addFlashMessagesToArray($this, [
                    'comBraDepList' => [
                        'DEPARTMENT_LIST' => EntityHelper::getTableList($this->adapter, Department::TABLE_NAME, [Department::DEPARTMENT_ID, Department::DEPARTMENT_NAME, Department::COMPANY_ID, Department::BRANCH_ID], [Department::STATUS => "E"])
                    ],
                    'departmentId' => $departmentId
        ]);
    }

    public function branchWiseAction() {
        $branchId = (int) $this->params()->fromRoute('id1');
        return Helper::addFlashMessagesToArray($this, [
                    'comBraDepList' => [
                        'BRANCH_LIST' => EntityHelper::getTableList($this->adapter, Branch::TABLE_NAME, [Branch::BRANCH_ID, Branch::BRANCH_NAME], [Branch::STATUS => "E"])
                    ],
                    'branchId' => $branchId
        ]);
    }

    public function departmentWiseDailyAction() {
        $monthId = (int) $this->params()->fromRoute('id1');
        $departmentId = (int) $this->params()->fromRoute('id2');


        $monthList = $this->reportRepo->getMonthList();
        return Helper::addFlashMessagesToArray($this, [
                    'comBraDepList' => [
                        'DEPARTMENT_LIST' => EntityHelper::getTableList($this->adapter, Department::TABLE_NAME, [Department::DEPARTMENT_ID, Department::DEPARTMENT_NAME, Department::COMPANY_ID, Department::BRANCH_ID], [Department::STATUS => "E"])
                    ],
                    'monthList' => $monthList,
                    'monthId' => $monthId,
                    'departmentId' => $departmentId
        ]);
    }

    public function employeeWiseAction() {

        $employeeId = (int) $this->params()->fromRoute('id1');
        $employeeList = $this->reportRepo->getEmployeeList();

        return Helper::addFlashMessagesToArray($this, [
                    'comBraDepList' => $this->getComBraDepList(),
                    'employeeList' => $employeeList,
                    'employeeId' => $employeeId
        ]);
    }

    public function withOvertimeAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $reportData = $this->reportRepo->reportWithOT($data);
                return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function employeeWiseDailyReportAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();

                $employeeId = $postedData['employeeId'];
                if (!isset($employeeId)) {
                    throw new Exception("parameter employeeId is required");
                }

                $reportData = $this->reportRepo->employeeWiseDailyReport($employeeId);
                return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function departmentWiseDailyReportAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();

                $departmentId = $postedData['departmentId'];
                if (!isset($departmentId)) {
                    throw new Exception("parameter departmentId is required");
                }
                $monthId = $postedData['monthId'];
                if (!isset($monthId)) {
                    throw new Exception("parameter monthId is required");
                }

                $reportData = $this->reportRepo->departmentWiseDailyReport($monthId, $departmentId);
                return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function departmentWiseMonthReportAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();

                $departmentId = $postedData['departmentId'];
                if (!isset($departmentId)) {
                    throw new Exception("parameter departmentId is required");
                }
                $reportData = $this->reportRepo->departmentWiseEmployeeMonthReport($departmentId);
                return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function branchWiseMonthReportAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();

                $branchId = $postedData['branchId'];
                if (!isset($branchId)) {
                    throw new Exception("parameter branchId is required");
                }
                $reportData = $this->reportRepo->branchWiseEmployeeMonthReport($branchId);
                return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function departmentMonthReportAction() {
        $data = $this->reportRepo->departmentMonthReport();
        return new CustomViewModel(['success' => true, 'data' => $data, 'error' => null]);
    }

    private function getComBraDepList() {
        $cbd = $this->reportRepo->getCompanyBranchDepartment();
        $comBraDepList = [];
        foreach ($cbd as $row) {
            if (isset($comBraDepList[$row['COMPANY_ID']])) {
                if (isset($comBraDepList[$row['COMPANY_ID']]['BRANCH_LIST'][$row['BRANCH_ID']])) {
                    $comBraDepList[$row['COMPANY_ID']]['BRANCH_LIST'][$row['BRANCH_ID']]['DEPARTMENT_LIST'][$row['DEPARTMENT_ID']] = [
                        'DEPARTMENT_ID' => $row['DEPARTMENT_ID'],
                        'DEPARTMENT_NAME' => $row['DEPARTMENT_NAME']
                    ];
                } else {
                    $comBraDepList[$row['COMPANY_ID']]['BRANCH_LIST'][$row['BRANCH_ID']] = [
                        'BRANCH_ID' => $row['BRANCH_ID'],
                        'BRANCH_NAME' => $row['BRANCH_NAME'],
                        'DEPARTMENT_LIST' => [
                            $row['DEPARTMENT_ID'] => [
                                'DEPARTMENT_ID' => $row['DEPARTMENT_ID'],
                                'DEPARTMENT_NAME' => $row['DEPARTMENT_ID']
                            ]
                        ]
                    ];
                }
            } else {
                $comBraDepList[$row['COMPANY_ID']] = [
                    'COMPANY_ID' => $row['COMPANY_ID'],
                    'COMPANY_NAME' => $row['COMPANY_NAME'],
                    'BRANCH_LIST' => [
                        $row['BRANCH_ID'] => [
                            'BRANCH_ID' => $row['BRANCH_ID'],
                            'BRANCH_NAME' => $row['BRANCH_NAME'],
                            'DEPARTMENT_LIST' => [
                                $row['DEPARTMENT_ID'] => [
                                    'DEPARTMENT_ID' => $row['DEPARTMENT_ID'],
                                    'DEPARTMENT_NAME' => $row['DEPARTMENT_ID']
                                ]
                            ]
                        ]
                    ]
                ];
            }
        }
        return $comBraDepList;
    }

    public function leaveReportAction() {

        $customFormElement = new Select();
        $customFormElement->setName("status");
        $custom = array(
            "EMP" => "Employee Wise",
            "BRA" => "Branch Wise",
            "DEP" => "Department Wise",
            "DES" => "Designation Wise",
            "POS" => "Position Wise",
        );
        $customFormElement->setValueOptions($custom);
        $customFormElement->setAttributes(["id" => "customWise", "class" => "form-control"]);
        $customFormElement->setLabel("Custom");

        $allLeave = $this->reportRepo->fetchAllLeave();
        return Helper::addFlashMessagesToArray($this, [
                    'customWise' => $customFormElement,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'allLeave' => $allLeave
        ]);
    }

    public function HireAndFireReportAction() {
        $nepaliMonth = $this->reportRepo->FetchNepaliMonth();
        return Helper::addFlashMessagesToArray($this, [
                    'nepaliMonth' => $nepaliMonth
        ]);
    }

    public function getLeaveReportWSAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("must be a post request.");
            }
            $data = $request->getPost();

            $customWise = $data['customWise'];


            switch ($customWise) {
                case 'EMP':
                    $reportData = $this->reportRepo->filterLeaveReportEmployee($data);
                    break;
                case 'BRA':
                    $reportData = $this->reportRepo->filterLeaveReportBranch($data);
                    break;
                case 'DEP':
                    $reportData = $this->reportRepo->filterLeaveReportDepartmnet($data);
                    break;
                case 'DES':
                    $reportData = $this->reportRepo->filterLeaveReportDesignation($data);
                    break;
                case 'POS':
                    $reportData = $this->reportRepo->filterLeaveReportPosition($data);
                    break;
            }

            return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function getHireFireReportAction() {
        try {
            $request = $this->getRequest();
            $Postdata = $request->getPost();
            $data = json_decode($Postdata['data']);
            $HireReport = $this->reportRepo->CalculateHireEmployees($data);
            return new CustomViewModel(['success' => true, 'data' => $HireReport, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function branchWiseDailyAction() {
        $monthId = (int) $this->params()->fromRoute('id1');
        $branchId = (int) $this->params()->fromRoute('id2');
        $monthList = $this->reportRepo->getMonthList();

        return Helper::addFlashMessagesToArray($this, [
                    'comBraList' => [
                        'BRANCH_LIST' => EntityHelper::getTableList($this->adapter, Branch::TABLE_NAME, [Branch::BRANCH_ID, Branch::BRANCH_NAME, Branch::COMPANY_ID], [Branch::STATUS => "E"])
                    ],
                    'monthList' => $monthList,
                    'monthId' => $monthId,
                    'branchId' => $branchId
        ]);
    }
    
    public function branchWiseDailyReportAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();
                
                $branchId = $postedData['branchId'];
                if (!isset($branchId)) {
                    throw new Exception("parameter branchId is required");
                }
                $monthId = $postedData['monthId'];
                if (!isset($monthId)) {
                    throw new Exception("parameter monthId is required");
                }

                $reportData = $this->reportRepo->branchWiseDailyReport($monthId, $branchId);
                return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
    
    

}
