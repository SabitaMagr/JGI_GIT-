<?php

namespace Travel\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\TravelRequestForm;
use SelfService\Model\TravelExpenseDetail;
use SelfService\Model\TravelRequest as TravelRequestModel;
use SelfService\Repository\TravelExpenseDtlRepository;
use SelfService\Repository\TravelRequestRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class TravelApply extends AbstractActionController {

    private $form;
    private $adapter;
    private $travelRequesteRepository;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->travelRequesteRepository = new TravelRequestRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new TravelRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        return $this->redirect()->toRoute("travelStatus");
    }

    public function addAction() {
        $request = $this->getRequest();
        $model = new TravelRequestModel();
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $expenseDtlList = $postData['data']['expenseDtlList'];
            $departureDate = $postData['data']['departureDate'];
            $returnedDate = $postData['data']['returnedDate'];
            $destination = $postData['data']['destination'];
            $purpose = $postData['data']['purpose'];
            $advanceAmount = $postData['data']['advanceAmount'];
            $requestedType = $postData['data']['requestedType'];
            $travelId = (int) $postData['data']['travelId'];
            $sumAllTotal = (float) $postData['data']['sumAllTotal'];
            $approverRole = $postData['data']['approverRole'];
            $employeeId = $postData['data']['employeeId'];
            $expenseDtlRepo = new TravelExpenseDtlRepository($this->adapter);
            $expenseDtlModel = new TravelExpenseDetail();

            $requestedAmt = $sumAllTotal;
            $model->fromDate = Helper::getExpressionDate($departureDate);
            $model->toDate = Helper::getExpressionDate($returnedDate);
            $model->destination = $destination;
            $model->purpose = $purpose;
            $model->requestedAmount = $requestedAmt;
            $model->departureDate = Helper::getExpressionDate($departureDate);
            $model->returnedDate = Helper::getExpressionDate($returnedDate);
            $model->advanceAmount = $advanceAmount;
            if (isset($travelId) && $travelId == 0) {
                $model->travelId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
                $model->employeeId = $employeeId;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $model->travelCode = "";
                $model->requestedType = 'ep';
                $model->approverRole = $approverRole;
                $this->travelRequesteRepository->add($model);
            } else if (isset($travelId) && $travelId>0) {
                $this->travelRequesteRepository->edit($model, $travelId);
            }else{
                return $this->redirect()->toRoute("travelRequest");
            }
            foreach ($expenseDtlList as $expenseDtl) {
                $transportType = $expenseDtl['transportType'];
                $id = (int) $expenseDtl['id'];
                $expenseDtlModel->departureDate = Helper::getExpressionDate($expenseDtl['departureDate']);
                $expenseDtlModel->departurePlace = $expenseDtl['departurePlace'];
                $expenseDtlModel->departureTime = Helper::getExpressionTime($expenseDtl['departureTime']);
                $expenseDtlModel->destinationDate = Helper::getExpressionDate($expenseDtl['destinationDate']);
                $expenseDtlModel->destinationPlace = $expenseDtl['destinationPlace'];
                $expenseDtlModel->destinationTime = Helper::getExpressionTime($expenseDtl['destinationTime']);
                $expenseDtlModel->transportType = $transportType['id'];
                $expenseDtlModel->fare = (float) $expenseDtl['fare'];
                $expenseDtlModel->allowance = ($expenseDtl['allowance'] != null) ? (float) $expenseDtl['allowance'] : null;
                $expenseDtlModel->localConveyence = ($expenseDtl['localConveyence'] != null) ? (float) $expenseDtl['localConveyence'] : null;
                $expenseDtlModel->miscExpenses = ($expenseDtl['miscExpense'] != null) ? (float) $expenseDtl['miscExpense'] : null;
                $expenseDtlModel->totalAmount = (float) $expenseDtl['total'];
                $expenseDtlModel->remarks = ($expenseDtl['remarks'] != null) ? $expenseDtl['remarks'] : null;
                $expenseDtlModel->status = 'E';
                $expenseDtlModel->fareFlag = ($expenseDtl['fareFlag']=="true" && $expenseDtl['fareFlag']!="")?'Y':'N';
                $expenseDtlModel->allowanceFlag = ($expenseDtl['allowanceFlag']=="true" && $expenseDtl['allowanceFlag']!="")?'Y':'N';
                $expenseDtlModel->localConveyenceFlag = ($expenseDtl['localConveyenceFlag']=="true" && $expenseDtl['localConveyenceFlag']!="")?'Y':'N';
                $expenseDtlModel->miscExpensesFlag = ($expenseDtl['miscExpenseFlag']=="true" && $expenseDtl['miscExpenseFlag']!="")?'Y':'N';
                if ($id == 0) {
                    $expenseDtlModel->id = ((int) Helper::getMaxId($this->adapter, TravelExpenseDetail::TABLE_NAME, TravelExpenseDetail::ID)) + 1;
                    $expenseDtlModel->travelId = ($requestedType == 'ad') ? $model->travelId : $travelId;
                    $expenseDtlModel->createdBy = $this->employeeId;
                    $expenseDtlModel->createdDate = Helper::getcurrentExpressionDate();
                    $expenseDtlRepo->add($expenseDtlModel);
                } else {
                    $expenseDtlModel->modifiedBy = (int) $this->employeeId;
                    $expenseDtlModel->modifiedDate = Helper::getcurrentExpressionDate();
                    $expenseDtlRepo->edit($expenseDtlModel, $id);
                }
            }
            try {
                HeadNotification::pushNotification(NotificationEvents::TRAVEL_APPLIED, $model, $this->adapter, $this);
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage($e->getMessage());
            }
            return new CustomViewModel(['success' => true, 'data' => ['msg' => 'Travel Request Successfully added!!!']]);
        } else {
            $id = (int) $this->params()->fromRoute('id');
            $currentRequestType = 'ep';
            if ($id === 0) {
                $id=0;
                $currentRequestType = 'ad';
            }
            return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'currentRequestType'=>$currentRequestType,
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ", false, true),
            ]);
        }
    }

}
