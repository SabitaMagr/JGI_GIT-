<?php

namespace ManagerService\Controller;

use Application\Helper\Helper;
use ManagerService\Repository\ManagerReportRepo;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;


class ManagerReportController extends AbstractActionController{
    
    private $repository;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->repository = new ManagerReportRepo($adapter);
        $authService = new AuthenticationService();
        $detail = $authService->getIdentity();
        $this->employeeId = $detail['employee_id'];
    }
   
    public function indexAction(){
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
            "WODO" => "Work on DAYOFF",
            "LI" => "Late In",
            "EO" => "Early Out"
        );
        
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "statusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");
        
        
        $employees= $this->repository->fetchAllEmployee($this->employeeId);
        
        $employeeFormElement= new Select();
        $employeeFormElement->setName('Employee');
        $employeeFormElement->setValueOptions($employees);
        $employeeFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeFormElement->setLabel("employee");
        


        return Helper::addFlashMessagesToArray($this, [
                    'attendanceList' => $attendanceList,
                    'employeeId' => $this->employeeId,
                    'status' => $statusFormElement,
                    'employeeFromElement'=>$employeeFormElement,
                    'currentEmployeeId'=>$this->employeeId
            
        ]);
    }
    
}
