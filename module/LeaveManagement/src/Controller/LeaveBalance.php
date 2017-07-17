<?php

namespace LeaveManagement\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use LeaveManagement\Form\LeaveApplyForm;
use LeaveManagement\Model\LeaveApply;
use LeaveManagement\Repository\LeaveBalanceRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use PHPExcel;
use PHPExcel_IOFactory;
use SelfService\Model\LeaveSubstitute;
use SelfService\Repository\LeaveRequestRepository;
use SelfService\Repository\LeaveSubstituteRepository;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class LeaveBalance extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;
    private $leaveRequestRepository;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new LeaveBalanceRepository($adapter);
        $this->leaveRequestRepository = new LeaveRequestRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction() {
        $leaveList = $this->repository->getAllLeave();

        $leaves = [];
        foreach ($leaveList as $leaveRow) {
            array_push($leaves, ['LEAVE_ENAME' => $leaveRow['LEAVE_ENAME']]);
        }

        $num = count($leaveList);

        return Helper::addFlashMessagesToArray($this, [
                    'leaveList' => $leaveList,
                    'leavesArrray' => $leaves,
                    'num' => $num,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function applyAction() {
        $request = $this->getRequest();
        $leaveId = (int) $this->params()->fromRoute('id');
        $employeeId = (int) $this->params()->fromRoute('eid');

        if ($leaveId === 0 && $employeeId === 0) {
            $postData = $request->getPost();
            $employeeId = $postData['employeeId'];
            $leaveId = $postData['leaveId'];
        }
        $this->initializeForm();

        $leaveBalanceDtl = $this->repository->getByEmpIdLeaveId($employeeId, $leaveId);
        $leaveRepository = new LeaveMasterRepository($this->adapter);
        $leaveDtl = $leaveRepository->fetchById($leaveId);

        $employeeRepository = new EmployeeRepository($this->adapter);
        $employeeDtl = $employeeRepository->fetchById($employeeId);
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            $leaveSubstitute = $postedData->leaveSubstitute;
            if ($this->form->isValid()) {
                $leaveRequest = new LeaveApply();
                $leaveRequest->exchangeArrayFromForm($this->form->getData());

                $leaveRequest->id = (int) Helper::getMaxId($this->adapter, LeaveApply::TABLE_NAME, LeaveApply::ID) + 1;
                $leaveRequest->employeeId = $employeeId;
                $leaveRequest->leaveId = $leaveId;
                $leaveRequest->startDate = Helper::getExpressionDate($leaveRequest->startDate);
                $leaveRequest->endDate = Helper::getExpressionDate($leaveRequest->endDate);
                $leaveRequest->requestedDt = Helper::getcurrentExpressionDate();
                $leaveRequest->status = "RQ";
                $this->leaveRequestRepository->add($leaveRequest);
                $this->flashmessenger()->addMessage("Leave Request Successfully added!!!");

                if ($leaveSubstitute !== null && $leaveSubstitute!=="") {
                    $leaveSubstituteModel = new LeaveSubstitute();
                    $leaveSubstituteRepo = new LeaveSubstituteRepository($this->adapter);


                    $leaveSubstituteModel->leaveRequestId = $leaveRequest->id;
                    $leaveSubstituteModel->employeeId = $leaveSubstitute;
                    $leaveSubstituteModel->createdBy = $this->employeeId;
                    $leaveSubstituteModel->createdDate = Helper::getcurrentExpressionDate();
                    $leaveSubstituteModel->status = 'E';

                    $leaveSubstituteRepo->add($leaveSubstituteModel);
                    try {
                        HeadNotification::pushNotification(NotificationEvents::LEAVE_SUBSTITUTE_APPLIED, $leaveRequest, $this->adapter, $this,$request->getBaseUrl());
                    } catch (Exception $e) {
                        $this->flashmessenger()->addMessage($e->getMessage());
                    }
                } else {
                    try {
                        HeadNotification::pushNotification(NotificationEvents::LEAVE_APPLIED, $leaveRequest, $this->adapter, $this,$request->getBaseUrl());
                    } catch (Exception $e) {
                        $this->flashmessenger()->addMessage($e->getMessage());
                    }
                }
                return $this->redirect()->toRoute("leavestatus");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeId' => $employeeId,
                    'leaveId' => $leaveId,
                    'employeeName' => $employeeDtl['FIRST_NAME'] . " " . $employeeDtl['MIDDLE_NAME'] . " " . $employeeDtl['LAST_NAME'],
                    'leaveName' => $leaveDtl['LEAVE_ENAME'],
                    'balance' => $leaveBalanceDtl['BALANCE'],
                    'allowHalfDay' => $leaveDtl['ALLOW_HALFDAY'],
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }

    public function recommendApproveList($employeeId) {
        $employeeRepository = new EmployeeRepository($this->adapter);
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $employeeId = $employeeId;
        $employeeDetail = $employeeRepository->fetchById($employeeId);
        $branchId = $employeeDetail['BRANCH_ID'];
        $departmentId = $employeeDetail['DEPARTMENT_ID'];
        $designations = $recommendApproveRepository->getDesignationList($employeeId);

        $recommender = array();
        $approver = array();
        foreach ($designations as $key => $designationList) {
            $withinBranch = $designationList['WITHIN_BRANCH'];
            $withinDepartment = $designationList['WITHIN_DEPARTMENT'];
            $designationId = $designationList['DESIGNATION_ID'];
            $employees = $recommendApproveRepository->getEmployeeList($withinBranch, $withinDepartment, $designationId, $branchId, $departmentId);

            if ($key == 1) {
                $i = 0;
                foreach ($employees as $employeeList) {
                    // array_push($recommender,$employeeList);
                    $recommender [$i]["id"] = $employeeList['EMPLOYEE_ID'];
                    $recommender [$i]["name"] = $employeeList['FIRST_NAME'] . " " . $employeeList['MIDDLE_NAME'] . " " . $employeeList['LAST_NAME'];
                    $i++;
                }
            } else if ($key == 2) {
                $i = 0;
                foreach ($employees as $employeeList) {
                    //array_push($approver,$employeeList);
                    $approver [$i]["id"] = $employeeList['EMPLOYEE_ID'];
                    $approver [$i]["name"] = $employeeList['FIRST_NAME'] . " " . $employeeList['MIDDLE_NAME'] . " " . $employeeList['LAST_NAME'];
                    $i++;
                }
            }
        }
        $responseData = [
            "recommender" => $recommender,
            "approver" => $approver
        ];
        return $responseData;
    }

    public function exportAction() {
        $leaveBalanceList = $this->repository->getOnlyCarryForwardedRecord();
        $num = count($leaveBalanceList);
        if ($num == 0) {
            $this->flashmessenger()->addMessage("There is no record found to export!!!");
            return $this->redirect()->toRoute("leavebalance");
        } else {

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);

            $rowCount = 1;
            $column = 'A';
            foreach ($leaveBalanceList[0] as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $key);
                $column++;
            }

            $rowCount = 2;
            foreach ($leaveBalanceList as $leaveBalanceRow) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $leaveBalanceRow['EMPLOYEE_ID']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $leaveBalanceRow['LEAVE_ID']);
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $leaveBalanceRow['PREVIOUS_YEAR_BAL']);
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $leaveBalanceRow['TOTAL_DAYS']);
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $leaveBalanceRow['BALANCE']);
                $rowCount++;
            }

//            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
//            header('Content-Disposition: attachment;filename="LeaveBalance.xlsx"');
//            header('Cache-Control: max-age=0');
//            ob_end_clean();
//            $objWriter->save('php://output');

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="LeaveBalance.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
    }

}
