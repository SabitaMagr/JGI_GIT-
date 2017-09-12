<?php

namespace AttendanceManagement\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Application\Model\Months;
use AttendanceManagement\Form\AttendanceByHrForm;
use AttendanceManagement\Model\AttendanceDetail;
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

        return Helper::addFlashMessagesToArray($this, [
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
                    $attendanceByHrModel = new AttendanceDetail();
                    $formData = $this->form->getData();
                    $attendanceByHrModel->exchangeArrayFromForm($formData);
                    $attendanceByHrModel->attendanceDt = Helper::getExpressionDate($attendanceByHrModel->attendanceDt);
                    $attendanceByHrModel->id = ((int) Helper::getMaxId($this->adapter, AttendanceDetail::TABLE_NAME, "ID")) + 1;
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

    public function pullAttendanceAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $take = $data['take'];
            $skip = $data['skip'];
            $page = $data['page'];
            $pageSize = $data['pageSize'];

            $max = $pageSize * $page;
            $min = $pageSize * ($page - 1);

            $employeeId = isset($data['employeeId']) ? $data['employeeId'] : -1;
            $companyId = isset($data['companyId']) ? $data['companyId'] : -1;
            $branchId = isset($data['branchId']) ? $data['branchId'] : -1;
            $departmentId = isset($data['departmentId']) ? $data['departmentId'] : -1;
            $positionId = isset($data['positionId']) ? $data['positionId'] : -1;
            $designationId = isset($data['designationId']) ? $data['designationId'] : -1;
            $serviceTypeId = isset($data['serviceTypeId']) ? $data['serviceTypeId'] : -1;
            $serviceEventTypeId = isset($data['serviceEventTypeId']) ? $data['serviceEventTypeId'] : -1;
            $employeeTypeId = isset($data['employeeTypeId']) ? $data['employeeTypeId'] : -1;
            $fromDate = $data['fromDate'];
            $toDate = $data['toDate'];
            $status = $data['status'];
            $missPunchOnly = ((int) $data['missPunchOnly'] == 1) ? true : false;
            $results = $this->repository->filterRecord($employeeId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $fromDate, $toDate, $status, $companyId, $employeeTypeId, false, $missPunchOnly, $min, $max);
            $total = $this->repository->filterRecordCount($employeeId, $branchId, $departmentId, $positionId, $designationId, $serviceTypeId, $serviceEventTypeId, $fromDate, $toDate, $status, $companyId, $employeeTypeId, false, $missPunchOnly);

            $result = [];
            $result['total'] = $total['TOTAL'];
            $result['results'] = Helper::extractDbData($results);

            return new CustomViewModel($result);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function bulkAttendanceWSAction() {
        try {
            $request = $this->getRequest();
            $postedData = $request->getPost();

            $action = $postedData['action'];
            $data = $postedData['data'];

            foreach ($data as $item) {
                $this->repository->manualAttendance($item['id'], $action);
            }
            return new CustomViewModel(['success' => true, 'data' => [], 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
