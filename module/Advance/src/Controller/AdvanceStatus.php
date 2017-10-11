<?php

namespace Advance\Controller;

use Advance\Repository\AdvanceStatusRepository;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use ManagerService\Repository\AdvanceApproveRepository;
use SelfService\Form\AdvanceRequestForm;
use SelfService\Model\AdvanceRequest;
use SelfService\Repository\AdvanceRequestRepository;
use Setup\Model\Advance;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

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

}
