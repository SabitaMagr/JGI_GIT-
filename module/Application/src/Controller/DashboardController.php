<?php

namespace Application\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\DashboardRepository;
use Application\Repository\TaskRepository;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use AttendanceManagement\Repository\AttendanceStatusRepository;
use Exception;
use HolidayManagement\Repository\HolidayRepository;
use Interop\Container\ContainerInterface;
use LeaveManagement\Repository\LeaveStatusRepository;
use Setup\Model\Branch;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use System\Repository\DashboardDetailRepo;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController {

    private $container;
    private $dashboardItems;
    private $adapter;
    private $roleId;
    private $userId;
    private $employeeId;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->dashboardItems = $container->get("config")['dashboard-items'];
        $this->adapter = $container->get(AdapterInterface::class);
    }

//    public function indexAction() {
//        $auth = new AuthenticationService();
//        $roleId = $auth->getStorage()->read()['role_id'];
//        $employeeId = $auth->getStorage()->read()['employee_id'];
//
//        $this->roleId = $roleId;
//        $this->employeeId = $employeeId;
//
//        $dashboardDetailRepo = new DashboardDetailRepo($this->adapter);
//        $result = $dashboardDetailRepo->fetchById($roleId);
//        $dashboards = [];
//        foreach ($result as $dashboard) {
//            array_push($dashboards, $dashboard);
//        }
//
//        $itemDetail = [];
//
//        foreach ($dashboards as $value) {
//            $itemDetail[$value['DASHBOARD']] = [
//                "path" => $this->dashboardItems[strtolower($value['DASHBOARD'])],
//                "data" => $this->getDashBoardData($value['DASHBOARD'], $value['ROLE_TYPE'])
//            ];
//        }
//
//        return new ViewModel(
//                Helper::addFlashMessagesToArray($this, [
//                    'dashboardItems' => $itemDetail
//        ]));
//    }

    public function indexAction() {
        $auth = new AuthenticationService();
        $employeeId = $auth->getStorage()->read()['employee_id'];
        $this->employeeId = $employeeId;
        $fiscalYear = $auth->getStorage()->read()['fiscal_year'];
        $dahsboardRepo = new DashboardRepository($this->adapter);
//        $employeeDetail = $dahsboardRepo->fetchEmployeeDashboardDetail($employeeId, $fiscalYear['START_DATE'], $fiscalYear['END_DATE']);
        $employeeDetail = $dahsboardRepo->fetchEmployeeDashboardDetail($employeeId, Helper::getCurrentDate(), Helper::getCurrentDate());


        $view = new ViewModel(Helper::addFlashMessagesToArray($this, array(
                    "employeeDetail" => $employeeDetail,
                    "upcomingHolidays" => $dahsboardRepo->fetchUpcomingHolidays($employeeDetail['EMPLOYEE_ID']),
                    "employeeNotice" => $dahsboardRepo->fetchEmployeeNotice(),
                    "employeeTask" => $dahsboardRepo->fetchEmployeeTask($employeeId),
                    "employeesBirthday" => $dahsboardRepo->fetchEmployeesBirthday(),
                    "employeeList" => $dahsboardRepo->fetchAllEmployee(),
                    "headCountGender" => $dahsboardRepo->fetchGenderHeadCount(),
                    "headCountDepartment" => $dahsboardRepo->fetchDepartmentHeadCount(),
                    "headCountLocation" => $dahsboardRepo->fetchLocationHeadCount(),
                    "departmentAttendance" => $dahsboardRepo->fetchDepartmentAttendance(),
                    'todoList' => $this->getTodoList()
        )));

        $returnData = $this->roleWiseView($auth);
        $view->setTemplate($returnData['template']);
        return $view;
    }

    public function roleWiseView(AuthenticationService $auth) {
        $roleId = $auth->getStorage()->read()['role_id'];
        $dashboardDetailRepo = new DashboardDetailRepo($this->adapter);
        $result = $dashboardDetailRepo->fetchById($roleId);
        $result = Helper::extractDbData($result, true);

        $type = null;
        foreach ($result as $dashboard) {
            if ($dashboard['DASHBOARD'] == 'dashboard') {
                $type = $dashboard['ROLE_TYPE'];
                break;
            }
        }
        $template = 'dashboard/employee';
        $data = null;
        switch ($type) {
            case 'E':
                $template = "dashboard/employee";


                break;
            case 'A':
                $template = "dashboard/hrm";


                break;
        }

        if ($template == null) {
            throw new Exception("dashboard not set");
        }
        return ['template' => $template, 'data' => $data];
    }

    public function getDashBoardData($item, $roleType) {
        $data = [];
        switch (strtolower($item)) {
            case 'holiday-list':
                $holidayRepo = new HolidayRepository($this->adapter);
                $today = Helper::getcurrentExpressionDate();
                switch ($roleType) {
                    case 'A':
                        $holidayRawList = $holidayRepo->fetchAll($today);
                        break;
                    case 'B':
                        $branchId = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::BRANCH_ID], HrEmployees::EMPLOYEE_ID . " = " . $this->employeeId, null)[$this->employeeId];
                        $holidayRawList = $holidayRepo->filter($branchId, null, Helper::getcurrentExpressionDate());
                        break;
                    case 'E':
                        $branchId = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::BRANCH_ID], HrEmployees::EMPLOYEE_ID . " = " . $this->employeeId, null)[$this->employeeId];
                        $genderId = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::GENDER_ID], HrEmployees::EMPLOYEE_ID . " = " . $this->employeeId, null)[$this->employeeId];
                        $holidayRawList = $holidayRepo->filter($branchId, $genderId, Helper::getcurrentExpressionDate());
                        break;
                }
                $holidayList = [];
                foreach ($holidayRawList as $holiday) {
                    array_push($holidayList, $holiday);
                }
                $data["holidayList"] = $holidayList;
                break;
            case 'attendance-request':
                switch ($roleType) {
                    case 'A':
                        $attendanceStatusRepo = new AttendanceStatusRepository($this->adapter);
                        $attendanceReqRawList = $attendanceStatusRepo->getAllRequest('RQ');
                        break;
                    case 'B':
                        $branchId = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::BRANCH_ID], HrEmployees::EMPLOYEE_ID . " = " . $this->employeeId, null)[$this->employeeId];
                        $attendanceStatusRepo = new AttendanceStatusRepository($this->adapter);
                        $attendanceReqRawList = $attendanceStatusRepo->getAllRequest('RQ', $branchId);
                        break;
                    case 'E':
                        $attendanceStatusRepo = new AttendanceStatusRepository($this->adapter);
                        $attendanceReqRawList = $attendanceStatusRepo->getAllRequest('RQ', null, $this->employeeId);
                        break;
                }
                $attendanceReqList = [];
                foreach ($attendanceReqRawList as $attendanceReq) {
                    array_push($attendanceReqList, $attendanceReq);
                }
                $data['attendanceRequestList'] = $attendanceReqList;
                break;
            case 'leave-apply':
                switch ($roleType) {
                    case 'A':
                        $attendanceStatusRepo = new LeaveStatusRepository($this->adapter);
                        $leaveApplyRawList = $attendanceStatusRepo->getAllRequest('RQ');
                        break;
                    case 'B':
                        $branchId = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::BRANCH_ID], HrEmployees::EMPLOYEE_ID . " = " . $this->employeeId, null)[$this->employeeId];
                        $attendanceStatusRepo = new LeaveStatusRepository($this->adapter);
                        $leaveApplyRawList = $attendanceStatusRepo->getAllRequest('RQ', null, $branchId);
                        break;
                    case 'E':
                        $attendanceStatusRepo = new LeaveStatusRepository($this->adapter);
                        $leaveApplyRawList = $attendanceStatusRepo->getAllRequest('RQ', null, null, $this->employeeId);
                        break;
                }
                $leaveApplyList = [];

                foreach ($leaveApplyRawList as $leaveApply) {
                    array_push($leaveApplyList, $leaveApply);
                }
                $data['leaveApplyList'] = $leaveApplyList;
                break;
            case 'present-absent':
                switch ($roleType) {
                    case 'A':
                        $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
                        $presentEmpRawList = $attendanceDetailRepo->getEmployeesAttendanceByDate(Helper::getcurrentExpressionDate(), TRUE);
                        break;
                    case 'B':
                        $branchId = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::BRANCH_ID], HrEmployees::EMPLOYEE_ID . " = " . $this->employeeId, null)[$this->employeeId];
                        $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
                        $presentEmpRawList = $attendanceDetailRepo->getEmployeesAttendanceByDate(Helper::getcurrentExpressionDate(), TRUE, $branchId);
                        break;
                    case 'E':
                        $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
                        $presentEmpRawList = $attendanceDetailRepo->getEmployeesAttendanceByDate(Helper::getcurrentExpressionDate(), TRUE);
                        break;
                }
                $presentEmpList = [];
                foreach ($presentEmpRawList as $present) {
                    array_push($presentEmpList, $present);
                }
                $data['presentEmployees'] = $presentEmpList;

                $absentEmpRawList = $attendanceDetailRepo->getEmployeesAttendanceByDate(Helper::getcurrentExpressionDate(), FALSE);
                $absentEmpList = [];
                foreach ($absentEmpRawList as $absent) {
                    array_push($absentEmpList, $absent);
                }
                $data['absentEmployees'] = $absentEmpList;
                break;
            case 'emp-cnt-by-branch':
                $empRepo = new EmployeeRepository($this->adapter);
                $branchEmpCountRawList = $empRepo->branchEmpCount();
                $branchEmpCountList = [];

                foreach ($branchEmpCountRawList as $branchEmpCount) {
                    if ($branchEmpCount[Branch::BRANCH_ID] != null) {
                        $branchName = EntityHelper::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], Branch::BRANCH_ID . "=" . $branchEmpCount[Branch::BRANCH_ID])[$branchEmpCount[Branch::BRANCH_ID]];
                        $branchEmpCount[Branch::BRANCH_NAME] = $branchName;
                        array_push($branchEmpCountList, $branchEmpCount);
                    }
                }
                $data['empCountByBranch'] = $branchEmpCountList;
                break;

            case 'today-leave':
                $leaveStatusRepo = new LeaveStatusRepository($this->adapter);
                $today = Helper::getcurrentExpressionDate();
                $approvedLeaveRawList = $leaveStatusRepo->getAllRequest('AP', $today);
                $approvedLeaveList = [];

                foreach ($approvedLeaveRawList as $approvedLeave) {
                    array_push($approvedLeaveList, $approvedLeave);
                }
                $data['approvedLeaveList'] = $approvedLeaveList;
                break;
            case 'birthdays':
                $employeeRepository = new EmployeeRepository($this->adapter);
                $employeeRowList = $employeeRepository->getEmployeeListOfBirthday();
                $employeeList = [];
                foreach ($employeeRowList as $employeeData) {
                    array_push($employeeList, $employeeData);
                }
                $data['employeeList'] = $employeeList;
                break;
        }
        return $data;
    }

    public function fetchEmployeeCalendarDataAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {

                $auth = new AuthenticationService();
                $employeeId = $auth->getStorage()->read()['employee_id'];
                $dahsboardRepo = new DashboardRepository($this->adapter);

                $calendarData = $dahsboardRepo->fetchEmployeeCalendarData($employeeId);
                return new CustomViewModel(['success' => true, 'data' => $calendarData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function fetchEmployeeCalendarJsonFeedAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {

                $auth = new AuthenticationService();
                $employeeId = $auth->getStorage()->read()['employee_id'];
                $dahsboardRepo = new DashboardRepository($this->adapter);

                $startDate = $this->getRequest()->getPost('start');
                $endDate = $this->getRequest()->getPost('end');
                $calendarData = $dahsboardRepo->fetchEmployeeCalendarData($employeeId, $startDate, $endDate);
                $calendarJsonFeedArray = [];
                foreach ($calendarData as $eventData) {
                    if ('ABSENT' == $eventData['ATTENDANCE_STATUS']) {
                        $calendarJsonFeedArray[] = [
                            'title' => 'Absent',
                            'start' => $eventData['MONTH_DAY'],
                            'textColor' => '#cc0000',
                            'backgroundColor' => '#fff'
                        ];
                    }

                    if ($eventData['ATTENDANCE_DT']) {
                        $inOutTitle = "";
                        if ($eventData['IN_TIME']) {
                            $inOutTitle .= $eventData['IN_TIME'];
                        }
                        if ($eventData['OUT_TIME']) {
                            $inOutTitle .= ' ' . $eventData['OUT_TIME'];
                        }
                        // In/Out
                        $calendarJsonFeedArray[] = [
                            'title' => $inOutTitle,
                            'start' => $eventData['ATTENDANCE_DT'],
                            'textColor' => '#616161',
                            'backgroundColor' => '#fff'
                        ];

                        // Training
                        if ($eventData['TRAINING_NAME']) {
                            $calendarJsonFeedArray[] = [
                                'title' => $eventData['TRAINING_NAME'],
                                'start' => $eventData['TRAINING_START_DATE'],
                                'end' => $eventData['TRAINING_END_DATE'],
                                'textColor' => '#fff',
                                'backgroundColor' => '#39c7b8',
                            ];
                        }
                        // Leave
                        if ($eventData['LEAVE_ENAME']) {
                            $calendarJsonFeedArray[] = [
                                'title' => $eventData['LEAVE_ENAME'],
                                'start' => $eventData['ATTENDANCE_DT'],
                                'textColor' => '#fff',
                                'backgroundColor' => '#a7aeaf',
                            ];
                        }
                        // Tour
                        if ($eventData['TRAVEL_CODE']) {
                            $calendarJsonFeedArray[] = [
                                'title' => $eventData['TRAVEL_CODE'],
                                'start' => $eventData['TRAVEL_FROM_DATE'],
                                'end' => $eventData['TRAVEL_TO_DATE'],
                                'textColor' => '#fff',
                                'backgroundColor' => '#e89c0a',
                            ];
                        }
                    }
                }
                //return new CustomViewModel(['success' => true, 'data' => $calendarJsonFeedArray, 'error' => '']);
                return new CustomViewModel($calendarJsonFeedArray);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    private function getTodoList() {
        $taskRepo = new TaskRepository($this->adapter);
        $result = $taskRepo->fetchEmployeeTask($this->employeeId);
        $list = [];
        foreach ($result as $row) {
            $nrow['id'] = $row['TASK_ID'];
            $nrow['title'] = $row['TASK_TITLE'];
            $nrow['description'] = $row['TASK_EDESC'];
            $nrow['dueDate'] = $row['END_DATE'];
            if ($row['STATUS'] == 'C') {
                $done = true;
            } else {
                $done = false;
            }
            $nrow['done'] = $done;
            array_push($list, $nrow);
        }
        return $list;
    }

}
