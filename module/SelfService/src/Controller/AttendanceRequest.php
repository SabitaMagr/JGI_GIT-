<?php

namespace SelfService\Controller;

use Application\Helper\Helper;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\AttendanceRequestForm;
use SelfService\Model\AttendanceRequestModel;
use SelfService\Repository\AttendanceRequestRepository;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class AttendanceRequest extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;
    private $employeeId;
    private $authService;
    private $approver;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new AttendanceRequestRepository($adapter);

        $this->authService = new AuthenticationService();
        $detail = $this->authService->getIdentity();
        $this->employeeId = $detail['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $attendanceRequest = new AttendanceRequestForm();
        $this->form = $builder->createForm($attendanceRequest);
    }

    public function getRecommendApprover() {
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($this->employeeId);

        if ($empRecommendApprove != null) {
            $this->approver = $empRecommendApprove['RECOMMEND_BY'];
        } else {
            $result = $this->recommendApproveList();
            if (count($result['approver']) > 0) {
                $this->approver = $result['approver'][0]['id'];
            } else {
                $this->approver = null;
            }
        }
    }

    public function indexAction() {
        $attendanceStatus = [
            '-1' => 'All',
            'RQ' => 'Pending',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $attendanceStatusFormElement = new Select();
        $attendanceStatusFormElement->setName("attendanceStatus");
        $attendanceStatusFormElement->setValueOptions($attendanceStatus);
        $attendanceStatusFormElement->setAttributes(["id" => "attendanceRequestStatusId", "class" => "form-control"]);
        $attendanceStatusFormElement->setLabel("Attendance Request Status");

        return Helper::addFlashMessagesToArray($this, [
                    'attendanceStatus' => $attendanceStatusFormElement,
                    'employeeId' => $this->employeeId
        ]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model = new AttendanceRequestModel();
                $model->exchangeArrayFromForm($this->form->getData());
                $model->employeeId = $this->employeeId;
                $model->attendanceDt = Helper::getExpressionDate($model->attendanceDt);
                $model->id = ((int) Helper::getMaxId($this->adapter, $model::TABLE_NAME, "ID")) + 1;
                $model->inTime = Helper::getExpressionTime($model->inTime);
                $model->outTime = Helper::getExpressionTime($model->outTime);
                $model->status = "RQ";

                $this->repository->add($model);
                $this->flashmessenger()->addMessage("Attendance Request Submitted Successfully!!");
                try {
                    HeadNotification::pushNotification(NotificationEvents::ATTENDANCE_APPLIED, $model, $this->adapter, $this->plugin('url'));
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }

                return $this->redirect()->toRoute("attendancerequest");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'employeeId' => $this->employeeId,
                    'form' => $this->form
                        ]
        );
    }

    public function editAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("attendancerequest");
        }

        $request = $this->getRequest();
        $model = new AttendanceRequestModel();
        if (!$request->isPost()) {
            $model->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($model);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->attendanceDt = Helper::getExpressionDate($model->attendanceDt);
                $model->inTime = Helper::getExpressionTime($model->inTime);
                $model->outTime = Helper::getExpressionTime($model->outTime);
                $this->repository->edit($model, $id);
                $this->flashmessenger()->addMessage("Attendance Request Updated Successfully!!");
                return $this->redirect()->toRoute("attendancerequest");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                        ]
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('attendancerequest');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Attendance Request Successfully Cancelled!!!");
        return $this->redirect()->toRoute('attendancerequest');
    }

    public function viewAction() {
        $this->initializeForm();
        $this->getRecommendApprover();

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("attedanceapprove");
        }
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };
        $approverName = $fullName($this->approver);

        $request = $this->getRequest();
        $model = new AttendanceRequestModel();
        $detail = $this->repository->fetchById($id);
        $employeeName = $fullName($detail['EMPLOYEE_ID']);

        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DT'];
        $approved_by = $fullName($detail['APPROVED_BY']);
        $authApprover = ( $status == 'RQ' || $status == 'C' || ($status == 'R' && $approvedDT == null)) ? $approverName : $approved_by;

        if (!$request->isPost()) {
            $model->exchangeArrayFromDB($detail);
            $this->form->bind($model);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'approver' => $authApprover,
                    'status' => $detail['STATUS'],
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DT'],
        ]);
    }

    public function approverList() {
        $employeeRepository = new EmployeeRepository($this->adapter);
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $employeeId = $this->employeeId;
        $employeeDetail = $employeeRepository->fetchById($employeeId);
        $branchId = $employeeDetail['BRANCH_ID'];
        $departmentId = $employeeDetail['DEPARTMENT_ID'];
        $designations = $recommendApproveRepository->getDesignationList($employeeId);

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
                    $approver [$i]["id"] = $employeeList['EMPLOYEE_ID'];
                    $approver [$i]["name"] = $employeeList['FIRST_NAME'] . " " . $employeeList['MIDDLE_NAME'] . " " . $employeeList['LAST_NAME'];
                    $i++;
                }
            }
        }
        $responseData = [
            "approver" => $approver,
        ];
        return $responseData;
    }

}
