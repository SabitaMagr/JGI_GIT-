<?php

namespace Advance\Controller;

use Advance\Repository\AdvanceStatusRepository;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use ManagerService\Repository\AdvanceApproveRepository;
use SelfService\Form\AdvanceRequestForm;
use SelfService\Model\AdvanceRequest;
use Setup\Model\Advance;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class AdvanceStatus extends AbstractActionController {

    private $adapter;
    private $advanceApproveRepository;
    private $advanceStatusRepository;
    private $form;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->advanceApproveRepository = new AdvanceApproveRepository($adapter);
        $this->advanceStatusRepository = new AdvanceStatusRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new AdvanceRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $advanceFormElement = new Select();
        $advanceFormElement->setName("advance");
        $advances = EntityHelper::getTableKVListWithSortOption($this->adapter, Advance::TABLE_NAME, Advance::ADVANCE_ID, [Advance::ADVANCE_NAME], [Advance::STATUS => 'E'], "ADVANCE_NAME", "ASC", NULL, FALSE, TRUE);
        $advances1 = [-1 => "All"] + $advances;
        $advanceFormElement->setValueOptions($advances1);
        $advanceFormElement->setAttributes(["id" => "advanceId", "class" => "form-control"]);
        $advanceFormElement->setLabel("Advance Type");

        $advanceStatus = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected',
            'C' => 'Cancelled'
        ];
        $advanceStatusFormElement = new Select();
        $advanceStatusFormElement->setName("advanceStatus");
        $advanceStatusFormElement->setValueOptions($advanceStatus);
        $advanceStatusFormElement->setAttributes(["id" => "advanceRequestStatusId", "class" => "form-control"]);
        $advanceStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'advances' => $advanceFormElement,
                    'advanceStatus' => $advanceStatusFormElement,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("advanceStatus");
        }
        $advanceRequest = new AdvanceRequest();
        $request = $this->getRequest();

        $detail = $this->advanceApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $employeeId = $detail['EMPLOYEE_ID'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $recommApprove = ($detail['RECOMMENDER_ID'] == $detail['APPROVER_ID']) ? 1 : 0;


        $employeeName = $detail['FULL_NAME'];
        $authRecommender = $detail['RECOMMENDED_BY_NAME'] == null ? $detail['RECOMMENDER_NAME'] : $detail['RECOMMENDED_BY_NAME'];
        $authApprover = $detail['APPROVED_BY_NAME'] == null ? $detail['APPROVER_NAME'] : $detail['APPROVED_BY_NAME'];

        if (!$request->isPost()) {
            $advanceRequest->exchangeArrayFromDB($detail);
            $this->form->bind($advanceRequest);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $advanceRequest->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $advanceRequest->status = "R";
                $this->flashmessenger()->addMessage("Advance Request Rejected!!!");
            } else if ($action == "Approve") {
                $advanceRequest->status = "AP";
                $this->flashmessenger()->addMessage("Advamce Request Approved");
            }
            $advanceRequest->approvedBy = $this->employeeId;
            $advanceRequest->approvedRemarks = $reason;
            $this->advanceApproveRepository->edit($advanceRequest, $id);

            return $this->redirect()->toRoute("advanceStatus");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeId' => $employeeId,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DATE'],
                    'recommender' => $authRecommender,
                    'approver' => $authApprover,
                    'approvedDT' => $detail['APPROVED_DATE'],
                    'status' => $status,
                    'advances' => EntityHelper::getTableKVListWithSortOption($this->adapter, Advance::TABLE_NAME, Advance::ADVANCE_ID, [Advance::ADVANCE_NAME], [Advance::STATUS => "E"], Advance::ADVANCE_ID, "ASC", NULL, FALSE, TRUE),
                    'customRenderer' => Helper::renderCustomView(),
                    'recommApprove' => $recommApprove
        ]);
    }

    public function pullAdvanceRequestStatusListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $advanceStatusRepository = new AdvanceStatusRepository($this->adapter);
            if (key_exists('recomApproveId', $data)) {
                $recomApproveId = $data['recomApproveId'];
            } else {
                $recomApproveId = null;
            }
            $result = $advanceStatusRepository->getFilteredRecord($data, $recomApproveId);

            $recordList = [];
            $getRoleDtl = function($recommender, $approver, $recomApproveId) {
                if ($recomApproveId == $recommender) {
                    return 'RECOMMENDER';
                } else if ($recomApproveId == $approver) {
                    return 'APPROVER';
                } else {
                    return null;
                }
            };
            $getRole = function($recommender, $approver, $recomApproveId) {
                if ($recomApproveId == $recommender) {
                    return 2;
                } else if ($recomApproveId == $approver) {
                    return 3;
                } else {
                    return null;
                }
            };
            $fullName = function($id) {
                $empRepository = new EmployeeRepository($this->adapter);
                $empDtl = $empRepository->fetchById($id);
                $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
                return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
            };

            $getValue = function($status) {
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

            foreach ($result as $row) {
                $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
                $empRecommendApprove = $recommendApproveRepository->fetchById($row['EMPLOYEE_ID']);

                $status = $getValue($row['STATUS']);
                $statusId = $row['STATUS'];
                $approvedDT = $row['APPROVED_DATE'];

                $authRecommender = ($statusId == 'RQ' || $statusId == 'C') ? $row['RECOMMENDER'] : $row['RECOMMENDED_BY'];
                $authApprover = ($statusId == 'RC' || $statusId == 'RQ' || $statusId == 'C' || ($statusId == 'R' && $approvedDT == null)) ? $row['APPROVER'] : $row['APPROVED_BY'];

                $roleID = $getRole($authRecommender, $authApprover, $recomApproveId);
                $recommenderName = $fullName($authRecommender);
                $approverName = $fullName($authApprover);

                $role = [
                    'APPROVER_NAME' => $approverName,
                    'RECOMMENDER_NAME' => $recommenderName,
                    'YOUR_ROLE' => $getRoleDtl($authRecommender, $authApprover, $recomApproveId),
                    'ROLE' => $roleID
                ];
                if ($empRecommendApprove['RECOMMEND_BY'] == $empRecommendApprove['APPROVED_BY']) {
                    $role['YOUR_ROLE'] = 'Recommender\Approver';
                    $role['ROLE'] = 4;
                }
                $new_row = array_merge($row, ['STATUS' => $status]);
                $final_record = array_merge($new_row, $role);
                array_push($recordList, $final_record);
            }


            return new JsonModel([
                "success" => "true",
                "data" => $recordList,
                "num" => count($recordList),
                "recomApproveId" => $recomApproveId
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
