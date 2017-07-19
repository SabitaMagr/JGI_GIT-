<?php

namespace ManagerService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Model\AttendanceDetail;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use DateTime;
use Exception;
use ManagerService\Repository\TrainingApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\TrainingRequestForm;
use SelfService\Model\TrainingRequest;
use Setup\Repository\RecommendApproveRepository;
use Setup\Repository\TrainingRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class TrainingApproveController extends AbstractActionController {

    private $trainingApproveRepository;
    private $employeeId;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->trainingApproveRepository = new TrainingApproveRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new TrainingRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        //print_r($this->employeeId); die();
        $list = $this->trainingApproveRepository->getAllRequest($this->employeeId);

        $trainingApprove = [];
        $getValue = function($recommender, $approver) {
            if ($this->employeeId == $recommender) {
                return 'RECOMMENDER';
            } else if ($this->employeeId == $approver) {
                return 'APPROVER';
            }
        };
        $getStatusValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };

        $getValueComType = function($trainingTypeId) {
            if ($trainingTypeId == 'CC') {
                return 'Company Contribution';
            } else if ($trainingTypeId == 'CP') {
                return 'Company Personal';
            }
        };

        $getRole = function($recommender, $approver) {
            if ($this->employeeId == $recommender) {
                return 2;
            } else if ($this->employeeId == $approver) {
                return 3;
            }
        };
        foreach ($list as $row) {
            $requestedEmployeeID = $row['EMPLOYEE_ID'];
            $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
            $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);

            if ($row['TRAINING_ID'] != 0) {
                $row['START_DATE'] = $row['T_START_DATE'];
                $row['END_DATE'] = $row['T_END_DATE'];
                $row['DURATION'] = $row['T_DURATION'];
                $row['TRAINING_TYPE'] = $row['T_TRAINING_TYPE'];
                $row['TITLE'] = $row['TRAINING_NAME'];
            }

            $new_row = array_merge($row, [
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER']),
                'STATUS' => $getStatusValue($row['STATUS']),
                'TRAINING_TYPE' => $getValueComType($row['TRAINING_TYPE']),
            ]);

            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $new_row['YOUR_ROLE'] = 'Recommender\Approver';
                $new_row['ROLE'] = 4;
            }
            array_push($trainingApprove, $new_row);
        }
        return Helper::addFlashMessagesToArray($this, ['trainingApprove' => $trainingApprove, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("trainingApprove");
        }
        $trainingRequestModel = new TrainingRequest();
        $request = $this->getRequest();

        $detail = $this->trainingApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FIRST_NAME'] . " " . $detail['MIDDLE_NAME'] . " " . $detail['LAST_NAME'];
        $RECM_MN = ($detail['RECM_MN'] != null) ? " " . $detail['RECM_MN'] . " " : " ";
        $recommender = $detail['RECM_FN'] . $RECM_MN . $detail['RECM_LN'];
        $APRV_MN = ($detail['APRV_MN'] != null) ? " " . $detail['APRV_MN'] . " " : " ";
        $approver = $detail['APRV_FN'] . $APRV_MN . $detail['APRV_LN'];
        $MN1 = ($detail['MN1'] != null) ? " " . $detail['MN1'] . " " : " ";
        $recommended_by = $detail['FN1'] . $MN1 . $detail['LN1'];
        $MN2 = ($detail['MN2'] != null) ? " " . $detail['MN2'] . " " : " ";
        $approved_by = $detail['FN2'] . $MN2 . $detail['LN2'];
        $authRecommender = ($status == 'RQ') ? $recommender : $recommended_by;
        $authApprover = ($status == 'RC' || $status == 'RQ' || ($status == 'R' && $approvedDT == null)) ? $approver : $approved_by;
        $recommenderId = ($status == 'RQ') ? $detail['RECOMMENDER'] : $detail['RECOMMENDED_BY'];
        if ($detail['TRAINING_ID'] != 0) {
            $detail['START_DATE'] = $detail['T_START_DATE'];
            $detail['END_DATE'] = $detail['T_END_DATE'];
            $detail['DURATION'] = $detail['T_DURATION'];
            $detail['TRAINING_TYPE'] = $detail['T_TRAINING_TYPE'];
        }
        if (!$request->isPost()) {
            $trainingRequestModel->exchangeArrayFromDB($detail);
            $this->form->bind($trainingRequestModel);
        } else {
            $getData = $request->getPost();
            $action = $getData->submit;
            if ($role == 2) {
                $trainingRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                $trainingRequestModel->recommendedBy = $this->employeeId;
                if ($action == "Reject") {
                    $trainingRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Training Request Rejected!!!");
                } else if ($action == "Approve") {
                    $trainingRequestModel->status = "RC";
                    $this->flashmessenger()->addMessage("Training Request Approved!!!");
                }
                $trainingRequestModel->recommendedRemarks = $getData->recommendedRemarks;
                $this->trainingApproveRepository->edit($trainingRequestModel, $id);
                $trainingRequestModel->requestId = $id;
                try {
                    HeadNotification::pushNotification(($trainingRequestModel->status == 'RC') ? NotificationEvents::TRAINING_RECOMMEND_ACCEPTED : NotificationEvents::TRAINING_RECOMMEND_REJECTED, $trainingRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            } else if ($role == 3 || $role == 4) {
                $trainingRequestModel->approvedDate = Helper::getcurrentExpressionDate();
                $trainingRequestModel->approvedBy = $this->employeeId;
                if ($action == "Reject") {
                    $trainingRequestModel->status = "R";
                    $this->flashmessenger()->addMessage("Training Request Rejected!!!");
                } else if ($action == "Approve") {
                    $trainingRequestModel->status = "AP";
                    $this->flashmessenger()->addMessage("Training Request Approved");
                }
                if ($role == 4) {
                    $trainingRequestModel->recommendedBy = $this->employeeId;
                    $trainingRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
                }

                // to update back date changes
                $sDate = $detail['START_DATE'];
                $eDate = $detail['END_DATE'];
                $trainingId = $detail['TRAINING_ID'];
                $currDate = Helper::getCurrentDate();
                $begin = new DateTime($sDate);
                $end = new DateTime($eDate);
                $attendanceDetailModel = new AttendanceDetail();
                $attendanceDetailModel->trainingId = $trainingId;
                $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);

                //                start of transaction
                $connection = $this->adapter->getDriver()->getConnection();
                $connection->beginTransaction();
                try {
                    if (strtotime($sDate) <= strtotime($currDate)) {
                        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                            $trainingdate = $i->format("d-M-Y");
                            if (strtotime($trainingdate) <= strtotime($currDate)) {
                                $where = ["EMPLOYEE_ID" => $requestedEmployeeID, "ATTENDANCE_DT" => $trainingdate];
                                $attendanceDetailRepo->editWith($attendanceDetailModel, $where);
                            }
                        }
                    }
                    $trainingRequestModel->approvedRemarks = $getData->approvedRemarks;
                    $this->trainingApproveRepository->edit($trainingRequestModel, $id);
                    $trainingRequestModel->requestId = $id;
                    $connection->commit();
                } catch (exception $e) {
                    $connection->rollback();
                    echo "error message:" . $e->getMessage();
                }
//                end of transaction
                try {
                    HeadNotification::pushNotification(($trainingRequestModel->status == 'AP') ? NotificationEvents::TRAINING_APPROVE_ACCEPTED : NotificationEvents::TRAINING_APPROVE_REJECTED, $trainingRequestModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("trainingApprove");
        }
        $trainingTypes = array(
            'CP' => 'Company Personal',
            'CC' => 'Company Contribution'
        );
        $trainings = $this->getTrainingList($requestedEmployeeID);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeName' => $employeeName,
                    'requestedDate' => $detail['REQUESTED_DATE'],
                    'role' => $role,
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'status' => $status,
                    'recommendedBy' => $recommenderId,
                    'approvedDT' => $approvedDT,
                    'employeeId' => $this->employeeId,
                    'requestedEmployeeId' => $requestedEmployeeID,
                    'trainingIdSelected' => $detail['TRAINING_ID'],
                    'trainings' => $trainings["trainingKVList"],
                    'trainingTypes' => $trainingTypes,
        ]);
    }

    public function statusAction() {
        $status = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected'
        ];
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "requestStatusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'status' => $statusFormElement,
                    'recomApproveId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function getTrainingList($employeeId) {
        $trainingRepo = new TrainingRepository($this->adapter);
        $trainingResult = $trainingRepo->selectAll($employeeId);
        $trainingList = [];
        $allTrainings = [];
        foreach ($trainingResult as $trainingRow) {
            $trainingList[$trainingRow['TRAINING_ID']] = $trainingRow['TRAINING_NAME'] . " (" . $trainingRow['START_DATE'] . " to " . $trainingRow['END_DATE'] . ")";
            $allTrainings[$trainingRow['TRAINING_ID']] = $trainingRow;
        }
        return ['trainingKVList' => $trainingList, 'trainingList' => $allTrainings];
    }

}
