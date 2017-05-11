<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AttendanceManagement\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Repository\AttendanceStatusRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Mvc\Controller\AbstractActionController;


class AttendanceReportController extends AbstractActionController {
    
    private $adapter;
    private $repository;
    private $userId;
    private $employeeId;
    
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new AttendanceStatusRepository($adapter);
        $authService = new AuthenticationService();
        $recordDetail = $authService->getIdentity();
        $this->userId = $recordDetail['user_id'];
        $this->employeeId = $recordDetail['employee_id'];
    }
    
    public function indexAction() {
        echo 'attendance repor index action';
        die();
        $attendanceStatus = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'AP' => 'Approved',
            'R' => 'Rejected',
            'C' => 'Cancelled'
        ];
        $attendanceStatusFormElement = new Select();
        $attendanceStatusFormElement->setName("attendanceStatus");
        $attendanceStatusFormElement->setValueOptions($attendanceStatus);
        $attendanceStatusFormElement->setAttributes(["id" => "attendanceRequestStatusId", "class" => "form-control"]);
        $attendanceStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'attendanceStatus' => $attendanceStatusFormElement
        ]);
    }
    
    
    
}
