<?php

namespace Report\Controller;

use Application\Custom\CustomViewModel;
use Exception;
use Report\Repository\ReportRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class AllReportController extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function indexAction() {
//        echo 'this is index action controller';
//        die();
    }

    public function reportOneAction() {
        $reportRepo = new ReportRepository($this->adapter);
        $reportRepo->departmentWiseEmployeeMonthReport(1);
        $reportRepo->departmentMonthReport();
    }

    public function reportTwoAction() {
        
    }

    public function reportThreeAction() {
        
    }

    public function reportFourAction() {
        
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
                $reportRepo = new ReportRepository($this->adapter);
                $reportData = $reportRepo->departmentWiseEmployeeMonthReport(1);
                return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function departmentMonthReportAction() {

        $data = [
                [
                'DEPARTMENT_ID' => 1,
                'MONTH_ID' => 1,
                'ON_LEAVE' => 0,
                'IS_PRESENT' => 0,
                'IS_ABSENT' => 4,
                'DEPARTMENT_NAME' => "Information Technology",
                'MONTH_EDESC' => "Chaitra"
            ],
                [
                'DEPARTMENT_ID' => 1,
                'MONTH_ID' => 2,
                'ON_LEAVE' => 0,
                'IS_PRESENT' => 0,
                'IS_ABSENT' => 4,
                'DEPARTMENT_NAME' => "Information Technology",
                'MONTH_EDESC' => "Baishak"
            ],
                [
                'DEPARTMENT_ID' => 1,
                'MONTH_ID' => 3,
                'ON_LEAVE' => 0,
                'IS_PRESENT' => 0,
                'IS_ABSENT' => 4,
                'DEPARTMENT_NAME' => "Information Technology",
                'MONTH_EDESC' => "Ashad"
            ],
                [
                'DEPARTMENT_ID' => 2,
                'MONTH_ID' => 1,
                'ON_LEAVE' => 0,
                'IS_PRESENT' => 0,
                'IS_ABSENT' => 4,
                'DEPARTMENT_NAME' => "Support",
                'MONTH_EDESC' => "Chaitra"
            ],
                [
                'DEPARTMENT_ID' => 2,
                'MONTH_ID' => 2,
                'ON_LEAVE' => 0,
                'IS_PRESENT' => 0,
                'IS_ABSENT' => 4,
                'DEPARTMENT_NAME' => "Support",
                'MONTH_EDESC' => "Baishak"
            ],
                [
                'DEPARTMENT_ID' => 2,
                'MONTH_ID' => 3,
                'ON_LEAVE' => 0,
                'IS_PRESENT' => 0,
                'IS_ABSENT' => 4,
                'DEPARTMENT_NAME' => "Support",
                'MONTH_EDESC' => "Ashad"
            ],
                [
                'DEPARTMENT_ID' => 3,
                'MONTH_ID' => 1,
                'ON_LEAVE' => 0,
                'IS_PRESENT' => 0,
                'IS_ABSENT' => 4,
                'DEPARTMENT_NAME' => "Developer",
                'MONTH_EDESC' => "Chaitra"
            ],
                [
                'DEPARTMENT_ID' => 3,
                'MONTH_ID' => 2,
                'ON_LEAVE' => 0,
                'IS_PRESENT' => 0,
                'IS_ABSENT' => 4,
                'DEPARTMENT_NAME' => "Developer",
                'MONTH_EDESC' => "Baishak"
            ],
                [
                'DEPARTMENT_ID' => 3,
                'MONTH_ID' => 3,
                'ON_LEAVE' => 0,
                'IS_PRESENT' => 0,
                'IS_ABSENT' => 4,
                'DEPARTMENT_NAME' => "Developer",
                'MONTH_EDESC' => "Ashad"
            ],
        ];
        return new CustomViewModel(['success' => true, 'data' => $data, 'error' => null]);
    }

}
