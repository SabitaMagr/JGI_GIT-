<?php

/**
 * Created by PhpStorm.
 * User: himal
 * Date: 7/22/16
 * Time: 3:31 PM
 */

namespace Application\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use AttendanceManagement\Repository\AttendanceStatusRepository;
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

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->dashboardItems = $container->get("config")['dashboard-items'];
        $this->adapter = $container->get(AdapterInterface::class);
    }

    public function indexAction() {
        $auth = new AuthenticationService();
        $roleId = $auth->getStorage()->read()['role_id'];
        $userId = $auth->getStorage()->read()['user_id'];

        $this->roleId = $roleId;
        $this->userId = $userId;

        $dashboardDetailRepo = new DashboardDetailRepo($this->adapter);
        $result = $dashboardDetailRepo->fetchById($roleId);
        $dashboards = [];
        foreach ($result as $dashboard) {
            array_push($dashboards, $dashboard);
        }

        $itemDetail = [];

        foreach ($dashboards as $value) {

            $itemDetail[$value['DASHBOARD']] = [
                "path" => $this->dashboardItems[$value['DASHBOARD']],
                "data" => $this->getDashBoardData($value['DASHBOARD'], $value['ROLE_TYPE'])
            ];
        }

        return new ViewModel([
            'dashboardItems' => $itemDetail
        ]);
    }

    public function getDashBoardData($item, $roleType) {
        $data = [];
        switch ($item) {
            case 'holiday-list':
                $holidayRepo = new HolidayRepository($this->adapter);
                $today = Helper::getcurrentExpressionDate();
                switch ($roleType) {
                    case 'A':
                        $holidayRawList = $holidayRepo->fetchAll($today);
                        break;
                    case 'B':
                        $branchId = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::BRANCH_ID], HrEmployees::EMPLOYEE_ID . " = " . $this->userId, null)[$this->userId];
                        $holidayRawList = $holidayRepo->filter($branchId, null, Helper::getcurrentExpressionDate());
                        break;
                    case 'E':
                        $branchId = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::BRANCH_ID], HrEmployees::EMPLOYEE_ID . " = " . $this->userId, null)[$this->userId];
                        $genderId = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::GENDER_ID], HrEmployees::EMPLOYEE_ID . " = " . $this->userId, null)[$this->userId];
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
                $attendanceStatusRepo = new AttendanceStatusRepository($this->adapter);
                $attendanceReqRawList = $attendanceStatusRepo->getAllRequest('RQ');
                $attendanceReqList = [];
                foreach ($attendanceReqRawList as $attendanceReq) {
                    array_push($attendanceReqList, $attendanceReq);
                }
                $data['attendanceRequestList'] = $attendanceReqList;
                break;
            case 'leave-apply':
                $attendanceStatusRepo = new LeaveStatusRepository($this->adapter);
                $leaveApplyRawList = $attendanceStatusRepo->getAllRequest('RQ');
                $leaveApplyList = [];

                foreach ($leaveApplyRawList as $leaveApply) {
                    array_push($leaveApplyList, $leaveApply);
                }
                $data['leaveApplyList'] = $leaveApplyList;
                break;
            case 'present-absent':
                $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
                $presentEmpRawList = $attendanceDetailRepo->getEmployeesAttendanceByDate(Helper::getcurrentExpressionDate(), TRUE);
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

}
