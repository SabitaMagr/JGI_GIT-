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
use Training\Model\TrainingAssign;
use Training\Repository\TrainingAssignRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
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
//            $attendance = new Attendance();
//            $attendance->employeeId = $employee->employeeId;
//            $attendance->attendanceDt = Helper::getcurrentExpressionDate();
//            $attendanceRepo->addAttendance($attendance);

            $attendanceDetail = new AttendanceDetail();
            $attendanceDetail->attendanceDt = Helper::getcurrentExpressionDate();
            $attendanceDetail->employeeId = $employee->employeeId;
            $attendanceDetail->id = ((int) Helper::getMaxId($this->adapter, AttendanceDetail::TABLE_NAME, AttendanceDetail::ID)) + 1;

//commented for change of logic
//            $checkForHoliday = $this->checkForHoliday($employee, $this->date);
//            if ($checkForHoliday == null) {
//                $checkForleave = $this->checkForLeave($employee, $this->date);
//                if ($checkForleave == null) {
//                    $attendanceRepo->add($attendanceDetail);
//                } else {
//                    $attendanceDetail->leaveId = $checkForleave[LeaveApply::LEAVE_ID];
//                    $attendanceRepo->add($attendanceDetail);
//                }
//            } else {
//                echo $checkForHoliday[Holiday::HOLIDAY_ID];
//                $attendanceDetail->holidayId = $checkForHoliday[Holiday::HOLIDAY_ID];
//                $attendanceRepo->add($attendanceDetail);
//            }


            $checkForHoliday = $this->checkForHoliday($employee, $this->date);
            $checkForleave = $this->checkForLeave($employee, $this->date);
            $checkForTraining = $this->checkForTraining($employee, $this->date);
            if ($checkForHoliday != null) {
                $attendanceDetail->holidayId = $checkForHoliday[Holiday::HOLIDAY_ID];
            }
            if ($checkForleave != null) {
                $attendanceDetail->leaveId = $checkForleave[LeaveApply::LEAVE_ID];
            }

            if ($checkForTraining != null) {
                $attendanceDetail->trainingId = $checkForTraining[TrainingAssign::TRAINING_ID];
            }
            $attendanceRepo->add($attendanceDetail);
        }

        return [];
    }

    public function testAction() {
        print "Hello from console";
    }

    public function employeeAttendanceAction() {
        $request = $this->getRequest();
        $employeeId = $request->getParam('employeeId');
        $attendanceDt = $request->getParam('attendanceDt');
        $attendanceTime = $request->getParam('attendanceTime');


        $attendance = new Attendance();
        $attendance->employeeId = $employeeId;

//        $attendanceDt = "18-DEC-2016";
//        $attendanceTime = "08:11 AM";

        $attendance->attendanceDt = Helper::getExpressionDate($attendanceDt);
        $attendance->attendanceTime = Helper::getExpressionTime($attendanceTime);

        $attendanceRepo = new AttendanceDetailRepository($this->adapter);
        $check = $attendanceRepo->addAttendance($attendance);

        if ($check) {
            print "eId: $employeeId attendanceDt: $attendanceDt attendanceTime $attendanceTime";
        } else {
            print "Action unsuccessful";
        }
    }

    private function pullEmployeeList() {
        $employeeRepo = new EmployeeRepository($this->adapter);
        return $employeeRepo->fetchAllForAttendance();
    }

    private function checkForHoliday(HrEmployees $employee, Expression $date) {
        $holidayRepo = new HolidayRepository($this->adapter);
        return $holidayRepo->checkEmployeeOnHoliday($date, $employee->branchId, $employee->genderId);
    }

    private function checkForLeave(HrEmployees $employee, Expression $date) {
        $leaveRepo = new LeaveRequestRepository($this->adapter);
        return $leaveRepo->checkEmployeeLeave($employee->employeeId, $date);
    }

    private function checkForTraining(HrEmployees $employee, Expression $date) {
        $trainingRepo = new TrainingAssignRepository($this->adapter);
        return $trainingRepo->checkEmployeeTraining($employee->employeeId, $date);
    }

    private function checkForTravel(HrEmployees $employee, Expression $date) {
        
    }

}
