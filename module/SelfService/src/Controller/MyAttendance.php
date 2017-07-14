<?php

namespace SelfService\Controller;

use Application\Helper\Helper;
use SelfService\Repository\AttendanceRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class MyAttendance extends AbstractActionController {

    private $repository;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new AttendanceRepository($adapter);

        $authService = new AuthenticationService();
        $detail = $authService->getIdentity();
        $this->employeeId = $detail['employee_id'];
    }

    public function indexAction() {
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $status = array(
            "All" => "All Status",
            "P" => "Present Only",
            "A" => "Absent Only",
            "H" => "On Holiday",
            "L" => "On Leave",
            "T" => "On Training",
            "TVL" => "On Travel",
            "WOH" => "Work on Holiday",
            "WOD" => "Work on DAYOFF",
            "LI" => "Late In",
            "EO" => "Early Out"
        );
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "statusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");

        $attendanceList = $this->repository->fetchByEmpId($this->employeeId);
        $fiscal_year = $this->repository->getCurrentNeplaiMonthStartDateEndDate();
        return Helper::addFlashMessagesToArray($this, [
                    'attendanceList' => $attendanceList,
                    'employeeId' => $this->employeeId,
                    'status' => $statusFormElement,
                    'fiscalYear' => $fiscal_year
        ]);
    }
    
    

}
