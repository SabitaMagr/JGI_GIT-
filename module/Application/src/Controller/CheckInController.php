<?php
namespace Application\Controller;

use Application\Helper\Helper;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use System\Repository\UserSetupRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;

class CheckInController extends AbstractActionController{
    private $adapter;
    private $repository;
    private $appConfig;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    public function setEventManager(EventManagerInterface $events) {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/login');
        }, 100);
    }
    public function indexAction() {
        $userId = $this->params()->fromRoute('userId');
        $type = $this->params()->fromRoute('type');
        $userRepository = new UserSetupRepository($this->adapter);
        $userDetail = $userRepository->fetchById($userId)->getArrayCopy();
        $employeeId=$userDetail['EMPLOYEE_ID'];
        
        $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
        $todayAttendance = $attendanceDetailRepo->fetchByEmpIdAttendanceDT($employeeId, 'TRUNC(SYSDATE)');
        
        $shiftDetails = $attendanceDetailRepo->fetchEmployeeShfitDetails($employeeId);
        if (!$shiftDetails) {
            $shiftDetails = $attendanceDetailRepo->fetchEmployeeDefaultShift($employeeId);
        }
        
        return Helper::addFlashMessagesToArray($this, [
                    'username'=> $userDetail['USER_NAME'],
                    'password'=> $userDetail['PASSWORD'],
                    'type'=> $type,
                    'attendanceDetails'=> $todayAttendance,
                    'shiftDetails'=> $shiftDetails
            ]);
    }
}
