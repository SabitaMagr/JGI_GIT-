<?php

namespace Application\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\DashboardRepository;
use Application\Repository\MonthRepository;
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
    private $auth;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->dashboardItems = $container->get("config")['dashboard-items'];
        $this->adapter = $container->get(AdapterInterface::class);


        $this->auth = new AuthenticationService();
        $this->employeeId = $this->auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $monthRepo = new MonthRepository($this->adapter);
        $dashboardRepo = new DashboardRepository($this->adapter);
        $month = $monthRepo->fetchByDate(Helper::getcurrentExpressionDate());
        $employeeDetail = $dashboardRepo->fetchEmployeeDashboardDetail($this->employeeId, $month->FROM_DATE, Helper::getCurrentDate());
        $data = [
            "employeeDetail" => $employeeDetail,
            "upcomingHolidays" => $dashboardRepo->fetchUpcomingHolidays($this->employeeId),
            "employeeNotice" => $dashboardRepo->fetchEmployeeNotice($this->employeeId),
            "employeeTask" => $dashboardRepo->fetchEmployeeTask($this->employeeId),
            "employeesBirthday" => $dashboardRepo->fetchEmployeesBirthday(),
            'todoList' => $this->getTodoList(),
        ];
        $view = new ViewModel(Helper::addFlashMessagesToArray($this, $data));
        $view->setTemplate("dashboard/employee");
        return $view;
    }

    public function adminAction() {
        $dashboardRepo = new DashboardRepository($this->adapter);

        $employeeDetail = $dashboardRepo->fetchAdminDashboardDetail($this->employeeId, Helper::getCurrentDate());
        $data = [
            "employeeDetail" => $employeeDetail,
            "employeeNotice" => $dashboardRepo->fetchEmployeeNotice(),
            "employeeTask" => $dashboardRepo->fetchEmployeeTask($this->employeeId),
            "employeesBirthday" => $dashboardRepo->fetchEmployeesBirthday(),
            "employeeList" => $dashboardRepo->fetchAllEmployee(),
            "headCountGender" => $dashboardRepo->fetchGenderHeadCount(),
            "headCountDepartment" => $dashboardRepo->fetchDepartmentHeadCount(),
            "headCountLocation" => $dashboardRepo->fetchLocationHeadCount(),
            "departmentAttendance" => $dashboardRepo->fetchDepartmentAttendance(),
            'todoList' => $this->getTodoList(),
            "pendingLeave" => $dashboardRepo->fetchPendingLeave(),
            "employeeJoinCM" => $dashboardRepo->fetchEmployeeJoiningCurrentMonth(),
            "upcomingHolidays" => $dashboardRepo->fetchUpcomingHolidays(),
            "employeeContracts" => $dashboardRepo->fetchEmployeeContracts(),
            "newEmployees" => $dashboardRepo->fetchJoinedEmployees(),
            "leftEmployees" => $dashboardRepo->fetchLeftEmployees(),
        ];
        $view = new ViewModel(Helper::addFlashMessagesToArray($this, $data));
        $view->setTemplate("dashboard/hrm");
        return $view;
    }

    public function branchManagerAction() {
        $dashboardRepo = new DashboardRepository($this->adapter);

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->getById($this->employeeId);

        $empCompanyId = $employeeDetail['COMPANY_ID'];
        $empBranchId = $employeeDetail['BRANCH_ID'];

        $data = [
            "employeeDetail" => $dashboardRepo->fetchManagerDashboardDetail($this->employeeId, Helper::getCurrentDate()),
            "present" => $dashboardRepo->fetchPresentCount($empBranchId)['PRESENT'],
            "leave" => $dashboardRepo->fetchLeaveCount($empCompanyId, $empBranchId)['LEAVE'],
            "training" => $dashboardRepo->fetchTrainingCount($empCompanyId, $empBranchId)['TRAINING'],
            "travel" => $dashboardRepo->fetchTravelCount($empCompanyId, $empBranchId)['TRAVEL'],
            "WOH" => $dashboardRepo->fetchWOHCount($empCompanyId, $empBranchId)['WOH'],
            'lateIn' => $dashboardRepo->fetchLateInCount($empCompanyId, $empBranchId)['LATE_IN'],
            'earlyOut' => $dashboardRepo->fetchEarlyOutCount($empCompanyId, $empBranchId)['EARLY_OUT'],
            'missedPunch' => $dashboardRepo->fetchMissedPunchCount($empCompanyId, $empBranchId)['MISSED_PUNCH'],
            "employeeNotice" => $dashboardRepo->fetchEmployeeNotice($employeeDetail['EMPLOYEE_ID']),
            "employeeTask" => $dashboardRepo->fetchEmployeeTask($this->employeeId),
            "employeesBirthday" => $dashboardRepo->fetchEmployeesBirthday(),
            "employeeList" => $dashboardRepo->fetchAllEmployee($empCompanyId, $empBranchId),
            "headCountGender" => $dashboardRepo->fetchGenderHeadCount(),
            "headCountDepartment" => $dashboardRepo->fetchDepartmentHeadCount(),
            "headCountLocation" => $dashboardRepo->fetchLocationHeadCount(),
            "departmentAttendance" => $dashboardRepo->fetchDepartmentAttendance(),
            'todoList' => $this->getTodoList(),
            "pendingLeave" => $dashboardRepo->fetchPendingLeave($empCompanyId, $empBranchId),
            "employeeJoinCM" => $dashboardRepo->fetchEmployeeJoiningCurrentMonth($empCompanyId, $empBranchId),
            "upcomingHolidays" => $dashboardRepo->fetchUpcomingHolidays($employeeDetail['EMPLOYEE_ID']),
        ];
        $view = new ViewModel(Helper::addFlashMessagesToArray($this, $data));
        $view->setTemplate("dashboard/branch-manager");
        return $view;
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
