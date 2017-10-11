<?php

namespace ManagerService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use HolidayManagement\Model\Holiday;
use ManagerService\Repository\HolidayWorkApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\WorkOnHolidayForm;
use SelfService\Model\WorkOnHoliday;
use SelfService\Repository\HolidayRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class HolidayWorkApproveController extends AbstractActionController {

    private $holidayWorkApproveRepository;
    private $employeeId;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->holidayWorkApproveRepository = new HolidayWorkApproveRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new WorkOnHolidayForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $list = $this->holidayWorkApproveRepository->getAllRequest($this->employeeId);

        $holidayWorkApprove = [];
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

            $dataArray = [
                'FULL_NAME' => $row['FULL_NAME'],
                'FIRST_NAME' => $row['FIRST_NAME'],
                'MIDDLE_NAME' => $row['MIDDLE_NAME'],
                'LAST_NAME' => $row['LAST_NAME'],
                'FROM_DATE' => $row['FROM_DATE'],
                'FROM_DATE_N' => $row['FROM_DATE_N'],
                'TO_DATE' => $row['TO_DATE'],
                'TO_DATE_N' => $row['TO_DATE_N'],
                'DURATION' => $row['DURATION'],
                'REQUESTED_DATE' => $row['REQUESTED_DATE'],
                'REQUESTED_DATE_N' => $row['REQUESTED_DATE_N'],
                'REMARKS' => $row['REMARKS'],
                'HOLIDAY_ENAME' => $row['HOLIDAY_ENAME'],
                'STATUS' => $getStatusValue($row['STATUS']),
                'ID' => $row['ID'],
                'YOUR_ROLE' => $getValue($row['RECOMMENDER'], $row['APPROVER']),
                'ROLE' => $getRole($row['RECOMMENDER'], $row['APPROVER'])
            ];
            if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                $dataArray['YOUR_ROLE'] = 'Recommender\Approver';
                $dataArray['ROLE'] = 4;
            }
            array_push($holidayWorkApprove, $dataArray);
        }
        return Helper::addFlashMessagesToArray($this, ['holidayWorkApprove' => $holidayWorkApprove, 'id' => $this->employeeId]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');
        $role = $this->params()->fromRoute('role');

        if ($id === 0) {
            return $this->redirect()->toRoute("holidayWorkApprove");
        }
        $workOnHolidayModel = new WorkOnHoliday();
        $request = $this->getRequest();

        $detail = $this->holidayWorkApproveRepository->fetchById($id);

        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];
        $recommenderId = $detail['RECOMMENDED_BY'] == null ? $detail['RECOMMENDER_ID'] : $detail['RECOMMENDED_BY'];

        if (!$request->isPost()) {
            $workOnHolidayModel->exchangeArrayFromDB($detail);
            $this->form->bind($workOnHolidayModel);
        } else {
            $getData = $request->getPost();
            $action = $getData->submit;

            if ($role == 2) {
                $workOnHolidayModel->recommendedDate = Helper::getcurrentExpressionDate();
                $workOnHolidayModel->recommendedBy = $this->employeeId;
                if ($action == "Reject") {
                    $workOnHolidayModel->status = "R";
                    $this->flashmessenger()->addMessage("Work on Holiday Request Rejected!!!");
                } else if ($action == "Approve") {
                    $workOnHolidayModel->status = "RC";
                    $this->flashmessenger()->addMessage("Work on Holiday Request Approved!!!");
                }
                $workOnHolidayModel->recommendedRemarks = $getData->recommendedRemarks;
                $this->holidayWorkApproveRepository->edit($workOnHolidayModel, $id);
                try {
                    $workOnHolidayModel->id = $id;
                    HeadNotification::pushNotification(($workOnHolidayModel->status == 'RC') ? NotificationEvents::WORKONHOLIDAY_RECOMMEND_ACCEPTED : NotificationEvents::WORKONHOLIDAY_RECOMMEND_REJECTED, $workOnHolidayModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            } else if ($role == 3 || $role == 4) {
                $workOnHolidayModel->approvedDate = Helper::getcurrentExpressionDate();
                $workOnHolidayModel->approvedBy = $this->employeeId;
                if ($action == "Reject") {
                    $workOnHolidayModel->status = "R";
                    $this->flashmessenger()->addMessage("Work on Holiday Request Rejected!!!");
                } else if ($action == "Approve") {
                    try {
                        $this->wohAppAction($detail);
                        $this->flashmessenger()->addMessage("Work on Holiday Request Approved");
                    } catch (Exception $e) {
                        $this->flashmessenger()->addMessage("Work on Holiday Request Approved but reward not given as position is not defined.");
                    }
                    $workOnHolidayModel->status = "AP";
                }
                if ($role == 4) {
                    $workOnHolidayModel->recommendedBy = $this->employeeId;
                    $workOnHolidayModel->recommendedDate = Helper::getcurrentExpressionDate();
                }
                $workOnHolidayModel->approvedRemarks = $getData->approvedRemarks;
                $this->holidayWorkApproveRepository->edit($workOnHolidayModel, $id);
                try {
                    $workOnHolidayModel->id = $id;
                    HeadNotification::pushNotification(($workOnHolidayModel->status == 'AP') ? NotificationEvents::WORKONHOLIDAY_APPROVE_ACCEPTED : NotificationEvents::WORKONHOLIDAY_APPROVE_REJECTED, $workOnHolidayModel, $this->adapter, $this);
                } catch (Exception $e) {
                    $this->flashmessenger()->addMessage($e->getMessage());
                }
            }
            return $this->redirect()->toRoute("holidayWorkApprove");
        }
        $holidays = $this->getHolidayList($requestedEmployeeID);
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
                    'holidays' => $holidays["holidayKVList"],
                    'holidayObjList' => $holidays["holidayList"]
        ]);
    }

    public function statusAction() {
        $holidayFormElement = new Select();
        $holidayFormElement->setName("holiday");
        $holidays = EntityHelper::getTableKVListWithSortOption($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME], [Holiday::STATUS => 'E'], Holiday::HOLIDAY_ENAME, "ASC", NULL, FALSE, TRUE);
        $holidays1 = [-1 => "All"] + $holidays;
        $holidayFormElement->setValueOptions($holidays1);
        $holidayFormElement->setAttributes(["id" => "holidayId", "class" => "form-control"]);
        $holidayFormElement->setLabel("Holiday Type");

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
                    'holidays' => $holidayFormElement,
                    'status' => $statusFormElement,
                    'recomApproveId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function getHolidayList($employeeId) {
        $holidayRepo = new HolidayRepository($this->adapter);
        $holidayResult = $holidayRepo->selectAll($employeeId);
        $holidayList = [];
        $holidayObjList = [];
        foreach ($holidayResult as $holidayRow) {
            $holidayList[$holidayRow['HOLIDAY_ID']] = $holidayRow['HOLIDAY_ENAME'] . " (" . $holidayRow['START_DATE'] . " to " . $holidayRow['END_DATE'] . ")";
            $holidayObjList[$holidayRow['HOLIDAY_ID']] = $holidayRow;
        }
        return ['holidayKVList' => $holidayList, 'holidayList' => $holidayObjList];
    }

    private function wohAppAction($detail) {
        $this->holidayWorkApproveRepository->wohReward($detail['ID']);
    }

    public function batchApproveRejectAction() {
        $request = $this->getRequest();
        try {
            if (!$request->ispost()) {
                throw new Exception('the request is not post');
            }
            $action;
            $postData = $request->getPost()['data'];
            $postBtnAction = $request->getPost()['btnAction'];
            if ($postBtnAction == 'btnApprove') {
                $action = 'Approve';
            } elseif ($postBtnAction == 'btnReject') {
                $action = 'Reject';
            } else {
                throw new Exception('no action defined');
            }

            if ($postData == null) {
                throw new Exception('no selected rows');
            } else {
                foreach ($postData as $data) {
                    $workOnHolidayModel = new WorkOnHoliday();
                    $id = $data['id'];
                    $role = $data['role'];
                    $detail = $this->holidayWorkApproveRepository->fetchById($id);

                    if ($role == 2) {
                        $workOnHolidayModel->recommendedDate = Helper::getcurrentExpressionDate();
                        $workOnHolidayModel->recommendedBy = $this->employeeId;
                        if ($action == "Reject") {
                            $workOnHolidayModel->status = "R";
                        } else if ($action == "Approve") {
                            $workOnHolidayModel->status = "RC";
                        }
                        $this->holidayWorkApproveRepository->edit($workOnHolidayModel, $id);
                        try {
                            $workOnHolidayModel->id = $id;
                            HeadNotification::pushNotification(($workOnHolidayModel->status == 'RC') ? NotificationEvents::WORKONHOLIDAY_RECOMMEND_ACCEPTED : NotificationEvents::WORKONHOLIDAY_RECOMMEND_REJECTED, $workOnHolidayModel, $this->adapter, $this);
                        } catch (Exception $e) {
                            
                        }
                    } else if ($role == 3 || $role == 4) {
                        $workOnHolidayModel->approvedDate = Helper::getcurrentExpressionDate();
                        $workOnHolidayModel->approvedBy = $this->employeeId;
                        if ($action == "Reject") {
                            $workOnHolidayModel->status = "R";
                        } else if ($action == "Approve") {
                            $this->wohAppAction($detail);
                            $workOnHolidayModel->status = "AP";
                        }
                        if ($role == 4) {
                            $workOnHolidayModel->recommendedBy = $this->employeeId;
                            $workOnHolidayModel->recommendedDate = Helper::getcurrentExpressionDate();
                        }
                        $this->holidayWorkApproveRepository->edit($workOnHolidayModel, $id);
                        try {
                            $workOnHolidayModel->id = $id;
                            HeadNotification::pushNotification(($workOnHolidayModel->status == 'AP') ? NotificationEvents::WORKONHOLIDAY_APPROVE_ACCEPTED : NotificationEvents::WORKONHOLIDAY_APPROVE_REJECTED, $workOnHolidayModel, $this->adapter, $this);
                        } catch (Exception $e) {
                            
                        }
                    }
                }
            }
            $listData = $this->getAllList();
            return new CustomViewModel(['success' => true, 'data' => $listData]);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getAllList() {
        $list = $this->holidayWorkApproveRepository->getAllRequest($this->employeeId);
        Helper::extractDbData($list);
    }

}
