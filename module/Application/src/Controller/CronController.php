<?php

namespace Application\Controller;

use Application\Helper\Helper;
use AttendanceManagement\Model\Attendance;
use AttendanceManagement\Model\AttendanceDetail;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use HolidayManagement\Model\Holiday;
use HolidayManagement\Repository\HolidayRepository;
use LeaveManagement\Model\LeaveApply;
use SelfService\Repository\LeaveRequestRepository;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class CronController extends AbstractActionController {

    private $adapter;
    private $date;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->date = Helper::getcurrentExpressionDate();
    }

    public function indexAction() {

        $employeeList = $this->pullEmployeeList();
        $attendanceRepo = new AttendanceDetailRepository($this->adapter);
        foreach ($employeeList as $employee) {
            $attendance = new Attendance();
//            $attendance->employeeId = $employee->employeeId;
//            $attendance->attendanceDt = Helper::getcurrentExpressionDate();
//            $attendanceRepo->addAttendance($attendance);

            $attendanceDetail = new AttendanceDetail();
            $attendanceDetail->attendanceDt = $attendance->attendanceDt;
            $attendanceDetail->employeeId = $attendance->employeeId;
            $attendanceDetail->id = ((int) Helper::getMaxId($this->adapter, AttendanceDetail::TABLE_NAME, AttendanceDetail::ID)) + 1;

            $checkForHoliday = $this->checkForHoliday($employee, $this->date);
            if ($checkForHoliday == null) {
                $checkForleave = $this->checkForLeave($employee, $this->date);
                if ($checkForleave == null) {
                    $attendanceRepo->add($attendanceDetail);
                } else {
                    $attendanceDetail->leaveId = $checkForleave[LeaveApply::LEAVE_ID];
                    $attendanceRepo->add($attendanceDetail);
                }
            } else {
                echo $checkForHoliday[Holiday::HOLIDAY_ID];
                $attendanceDetail->holidayId = $checkForHoliday[Holiday::HOLIDAY_ID];
                $attendanceRepo->add($attendanceDetail);
            }
        }

        return [];
    }

    public function testAction() {
        print "Hello from console";
    }

    public function checkAction() {
        print "under process";
    }

    public function employeeAttendanceAction() {
        
    }

    private function pullEmployeeList() {
        $employeeRepo = new EmployeeRepository($this->adapter);
        return $employeeRepo->fetchAll();
    }

    private function checkForHoliday(HrEmployees $employee, $date) {
        $holidayRepo = new HolidayRepository($this->adapter);
        return $holidayRepo->checkEmployeeOnHoliday($date, $employee->branchId, $employee->genderId);
    }

    private function checkForLeave(HrEmployees $employee, $date) {
        $leaveRepo = new LeaveRequestRepository($this->adapter);
        return $leaveRepo->checkEmployeeLeave($employee->employeeId, $date);
    }

}
