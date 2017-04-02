<?php

namespace Report\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Exception;
use Report\Repository\ReportRepository;
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

    public function reportOneAction() {
//        $reportRepo = new ReportRepository($this->adapter);
//        $reportRepo->departmentMonthReport();
//        $reportRepo->departmentWiseEmployeeMonthReport(1);
//        print "<pre>";
//        print_r(
//                $reportRepo->departmentWiseDailyReport(21, 1)
//        );
//        exit;
    }

    public function reportTwoAction() {
        return Helper::addFlashMessagesToArray($this, [
                    'comBraDepList' => $this->getComBraDepList()
        ]);
    }

    public function reportThreeAction() {
        $monthList = $this->reportRepo->getMonthList();


        return Helper::addFlashMessagesToArray($this, [
                    'comBraDepList' => $this->getComBraDepList(),
                    'monthList' => $monthList
        ]);
    }

    public function reportFourAction() {
        $employeeList = $this->reportRepo->getEmployeeList();

        return Helper::addFlashMessagesToArray($this, [
                    'comBraDepList' => $this->getComBraDepList(),
                    'employeeList' => $employeeList
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
//                print "<pre>";
//                print $departmentId;
//                exit;
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

}
