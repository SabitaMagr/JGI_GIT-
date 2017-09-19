<?php

namespace Report\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Report\Repository\ReportRepository;
use Setup\Model\Department;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class AllReportController extends AbstractActionController {

    private $adapter;
    private $reportRepo;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->reportRepo = new ReportRepository($this->adapter);
    }

    public function indexAction() {
//        echo 'this is index action controller';
//        die();
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
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function HireAndFireReportAction() {
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function getLeaveReportWSAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();
                $allLeave = $this->reportRepo->fetchAllLeave();
                $leaveCount = count($allLeave);
                if ($leaveCount <= 0) {
                    throw new Exception('NO Leave found');
                }
//                $columHeader = [];
                $leaveData = '';
                $i = 1;
                foreach ($allLeave as $leave) {
//                    array_push($columHeader, $leave['LEAVE_ENAME']);
//                    $columHeader[$leave['LEAVE_ID']]=$leave['LEAVE_ENAME'];
                    $leaveData .= $leave['LEAVE_ID'];
                    if ($i < $leaveCount) {
                        $leaveData .= ',';
                    }
                    $i++;
                }
                $leavereport = $this->reportRepo->filterLeaveReport($postedData, $leaveData);
//                $columnData = $leavereport;
                $columnData = [];
                foreach ($leavereport as $report) {
                    $tempData = [
                        'EMPLOYEE_ID' => $report['EMPLOYEE_ID'],
                        'FULL_NAME' => $report['FULL_NAME']
                    ];
                    foreach ($allLeave as $leave) {
                        $tempData[$leave['LEAVE_TRIM_ENAME']] = $report[$leave['LEAVE_ID']];
                    }
                    array_push($columnData, $tempData);
                }
                $returnData = [
                    'columns' => $allLeave,
                    'data' => $columnData
                ];
                return new CustomViewModel(['success' => true, 'data' => $returnData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
