<?php

namespace Training\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use ManagerService\Repository\TrainingApproveRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Form\TrainingRequestForm;
use SelfService\Model\TrainingRequest;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class TrainingStatusController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(TrainingApproveRepository::class);
        $this->initializeForm(TrainingRequestForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $searchQuery = $request->getPost();
                $rawList = $this->repository->getListAdmin((array) $searchQuery);
                $list = iterator_to_array($rawList, false);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $statusSE = $this->getStatusSelectElement(['name' => 'status', 'id' => 'status', 'class' => 'form-control', 'label' => 'Status']);
        return $this->stickFlashMessagesTo([
                    'status' => $statusSE,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'acl' => $this->acl,
                    'employeeDetail' => $this->storageData['employee_detail']
        ]);
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute('id');
        if ($id === 0) {
            return $this->redirect()->toRoute("trainingApprove");
        }
        $trainingRequestModel = new TrainingRequest();
        $request = $this->getRequest();

        $detail = $this->repository->fetchById($id);
        if ($request->isPost()) {
            $getData = $request->getPost();
            $action = $getData->submit;
            $trainingRequestModel->recommendedBy = $this->employeeId;
            $trainingRequestModel->recommendedDate = Helper::getcurrentExpressionDate();
            $trainingRequestModel->approvedDate = Helper::getcurrentExpressionDate();
            $trainingRequestModel->approvedBy = $this->employeeId;
            if ($action == "Reject") {
                $trainingRequestModel->status = "R";
                $this->flashmessenger()->addMessage("Training Request Rejected!!!");
            } else if ($action == "Approve") {
                $trainingRequestModel->status = "AP";
                $this->flashmessenger()->addMessage("Training Request Approved");
            }
            $this->repository->edit($trainingRequestModel, $id);
            $trainingRequestModel->requestId = $id;
            try {
                HeadNotification::pushNotification(($trainingRequestModel->status == 'AP') ? NotificationEvents::TRAINING_APPROVE_ACCEPTED : NotificationEvents::TRAINING_APPROVE_REJECTED, $trainingRequestModel, $this->adapter, $this);
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage($e->getMessage());
            }
            return $this->redirect()->toRoute("trainingStatus");
        }
        $trainingRequestModel->exchangeArrayFromDB($detail);
        $this->form->bind($trainingRequestModel);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'detail' => $detail,
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }

}
