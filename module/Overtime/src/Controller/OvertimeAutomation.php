<?php

namespace Overtime\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Overtime\Repository\OvertimeAutomationRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class OvertimeAutomation extends AbstractActionController {

    private $adapter;
    private $otAutomationRepo;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->otAutomationRepo = new OvertimeAutomationRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $overtimeCompulsoryList = $this->otAutomationRepo->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['overtimeCompulsoryList' => Helper::extractDbData($overtimeCompulsoryList)]);
    }

    public function wizardAction() {
        $id = (int) $this->params()->fromRoute("id");
        $editData = null;
        if ($id !== 0) {
            $editData['compulsoryOTSetup'] = $this->otAutomationRepo->fetchById($id);
            $editData['assignedEmployees'] = [];
            $employees = $this->otAutomationRepo->fetchAssignedEmployees($id);
            foreach ($employees as $employee) {
                array_push($editData['assignedEmployees'], $employee['EMPLOYEE_ID']);
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'editData' => $editData
                        ]
        );
    }

    public function handleWizardAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $postedData = $request->getPost();

            $earlyOvertimeHour = $postedData['earlyOvertimeHour'];
            $lateOvertimeHour = $postedData['lateOvertimeHour'];
            $startDate = $postedData['startDate'];
            $endDate = $postedData['endDate'];
            $employeeList = $postedData['employeeList'];
            $compulsoryOvertimeId = $postedData['compulsoryOvertimeId'];

            $result = $this->otAutomationRepo->wizardProcedure(Helper::hoursToMinutes($earlyOvertimeHour), Helper::hoursToMinutes($lateOvertimeHour), $startDate, $endDate, $employeeList, $this->employeeId, $compulsoryOvertimeId);
            return new CustomViewModel(['success' => true, 'data' => $result, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
