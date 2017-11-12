<?php

namespace AttendanceManagement\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Model\AttendanceDetail;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use AttendanceManagement\Repository\AttendanceStatusRepository;
use Exception;
use SelfService\Form\AttendanceRequestForm;
use SelfService\Model\AttendanceRequestModel;
use SelfService\Repository\AttendanceRequestRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class AttendanceStatus extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(AttendanceStatusRepository::class);
        $this->initializeForm(AttendanceRequestForm::class);
    }

    public function indexAction() {
        $statusSE = $this->getStatusSelectElement(['name' => 'attendanceStatus', 'id' => 'attendanceRequestStatusId', "class" => "form-control", 'label' => 'Status']);
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'attendanceStatus' => $statusSE,
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail']
        ]);
    }

    public function viewAction() {
        $request = $this->getRequest();
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("attendancestatus");
        }
        $attendanceRequestRepository = new AttendanceRequestRepository($this->adapter);
        $detail = $attendanceRequestRepository->fetchById($id);

        $model = new AttendanceRequestModel();
        $employeeId = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FULL_NAME'];

        $status = $detail['STATUS'];
        $authApprover = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];

        $attendanceDetail = new AttendanceDetail();
        $attendanceRepository = new AttendanceDetailRepository($this->adapter);

        try {
            if (!$request->isPost()) {
                $model->exchangeArrayFromDB($detail);
                $this->form->bind($model);
            } else {
                $getData = $request->getPost();
                $reason = $getData->approvedRemarks;
                $action = $getData->submit;

                $model->approvedDt = Helper::getcurrentExpressionDate();

                if ($action == "Approve") {
                    $model->status = "AP";
                    $empAttDtl = $attendanceRepository->getDtlWidEmpIdDate($employeeId, $detail['ATTENDANCE_DT']);
                    if ($empAttDtl == null) {
                        $tempDate = $detail['ATTENDANCE_DT'];
                        throw new Exception("Attendance of employee with employeeId :$employeeId on $tempDate is not found.");
                    }

                    $attendanceDetail->inTime = Helper::getExpressionTime($detail['IN_TIME']);
                    $attendanceDetail->inRemarks = $detail['IN_REMARKS'];
                    $attendanceDetail->outTime = Helper::getExpressionTime($detail['OUT_TIME']);
                    $attendanceDetail->outRemarks = $detail['OUT_REMARKS'];
                    $attendanceDetail->totalHour = $detail['TOTAL_HOUR'];


                    $attendanceRepository->editWith($attendanceDetail, [
                        AttendanceDetail::EMPLOYEE_ID => $employeeId,
                        AttendanceDetail::ATTENDANCE_DT => Helper::getExpressionDate($detail['ATTENDANCE_DT'])
                    ]);

                    $this->flashmessenger()->addMessage("Attendance Request Approved!!!");
                } else if ($action == "Reject") {
                    $model->status = "R";
                    $this->flashmessenger()->addMessage("Attendance Request Rejected!!!");
                }
                $model->approvedBy = $this->employeeId;
                $model->approvedRemarks = $reason;
                $attendanceRequestRepository->edit($model, $id);
                return $this->redirect()->toRoute("attendancestatus");
            }
            return Helper::addFlashMessagesToArray($this, [
                        'form' => $this->form,
                        'id' => $id,
                        'employeeName' => $employeeName,
                        'approver' => $authApprover,
                        'employeeId' => $employeeId,
                        'status' => $status,
                        'requestedDt' => $detail['REQUESTED_DT'],
            ]);
        } catch (\Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
            return Helper::addFlashMessagesToArray($this, [
                        'form' => $this->form,
                        'id' => $id,
                        'employeeName' => $employeeName,
                        'approver' => $authApprover,
                        'employeeId' => $employeeId,
                        'status' => $status,
                        'requestedDt' => $detail['REQUESTED_DT'],
            ]);
        }
    }

    public function pullAttendanceRequestStatusListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $attendanceStatusRepository = new AttendanceStatusRepository($this->adapter);
            $result = $attendanceStatusRepository->getAttenReqList($data);
            $recordList = Helper::extractDbData($result);
            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "num" => count($recordList)
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
