<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/29/16
 * Time: 12:47 PM
 */
namespace SelfService\Controller;

use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;
use SelfService\Repository\AttendanceRepository;

class MyAttendance extends AbstractActionController{
    private $repository;
    private $employeeId;
    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new AttendanceRepository($adapter);

        $authService = new AuthenticationService();
        $detail = $authService->getIdentity();
        $this->employeeId = $detail['employee_id'];

    }

    public function indexAction()
    {
        $attendanceList = $this->repository->fetchByEmpId($this->employeeId);
        return Helper::addFlashMessagesToArray($this, ['attendanceList' => $attendanceList,'employeeId'=>$this->employeeId]);
    }
}