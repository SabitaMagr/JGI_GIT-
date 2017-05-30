<?php

namespace AttendanceManagement\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Form\AttendanceByHrForm;
use AttendanceManagement\Model\AttendanceDetail as AttendanceByHrModel;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class AttendanceByHr extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new AttendanceDetailRepository($adapter);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $attendanceByHr = new AttendanceByHrForm();
        $this->form = $builder->createForm($attendanceByHr);
    }

    public function indexAction() {
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $status = array(
            "All" => "All Status",
            "P" => "Present Only",
            "A" => "Absent Only",
            "H" => "On Holiday",
            "L" => "On Leave"
        );
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "statusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");

        $attendanceList = $this->repository->fetchAll();
        $attendanceByHr = [];
        foreach ($attendanceList as $attendanceRow) {
            array_push($attendanceByHr, $attendanceRow);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'attendanceByHr' => $attendanceByHr,
                    'status' => $statusFormElement,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        try {


            if ($request->isPost()) {
                $this->form->setData($request->getPost());
                if ($this->form->isValid()) {
                    $attendanceByHrModel = new AttendanceByHrModel();
                    $formData = $this->form->getData();
                    $attendanceByHrModel->exchangeArrayFromForm($formData);
                    $attendanceByHrModel->attendanceDt = Helper::getExpressionDate($attendanceByHrModel->attendanceDt);
                    $attendanceByHrModel->id = ((int) Helper::getMaxId($this->adapter, AttendanceByHrModel::TABLE_NAME, "ID")) + 1;
                    $attendanceByHrModel->inTime = Helper::getExpressionTime($attendanceByHrModel->inTime);
                    $attendanceByHrModel->outTime = Helper::getExpressionTime($attendanceByHrModel->outTime);

                    $employeeId = $attendanceByHrModel->employeeId;
                    $attendanceDt = $formData['attendanceDt'];

                    $previousDtl = $this->repository->getDtlWidEmpIdDate($employeeId, $attendanceDt);

                    if ($previousDtl == null) {
                        throw new Exception("Attendance of employee with employeeId :$employeeId on $attendanceDt is not found.");
                    } else {
                        $this->repository->edit($attendanceByHrModel, $previousDtl['ID']);
                    }

                    $this->flashmessenger()->addMessage("Attendance Submitted Successfully!!");
                    return $this->redirect()->toRoute("attendancebyhr");
                }
            }
            return Helper::addFlashMessagesToArray($this, [
                        'form' => $this->form,
                        'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ", FALSE, TRUE)
                            ]
            );
        } catch (Exception $e) {
            $this->flashmessenger()->addMessage("Attendance Submit Failed!!");
            $this->flashmessenger()->addMessage($e->getMessage());
            return Helper::addFlashMessagesToArray($this, [
                        'form' => $this->form,
                        'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ", FALSE, TRUE)
                            ]
            );
//            return $this->redirect()->toRoute("attendancebyhr");
        }
    }

    public function editAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("attendancebyhr");
        }

        $request = $this->getRequest();
        $attendanceByHrModel = new AttendanceByHrModel();
        if (!$request->isPost()) {
            $attendanceByHrModel->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($attendanceByHrModel);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $attendanceByHrModel->exchangeArrayFromForm($this->form->getData());
                $attendanceByHrModel->inTime = Helper::getExpressionTime($attendanceByHrModel->inTime);
                $attendanceByHrModel->outTime = Helper::getExpressionTime($attendanceByHrModel->outTime);
                $this->repository->edit($attendanceByHrModel, $id);
                $this->flashmessenger()->addMessage("Attendance Updated Successfully!!");
                return $this->redirect()->toRoute("attendancebyhr");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", NULL, FALSE, TRUE)
                        ]
        );
    }

    public function deleteAction() {
        
    }

}
