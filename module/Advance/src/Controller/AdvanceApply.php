<?php

namespace Advance\Controller;

use Advance\Form\AdvanceRequestForm;
use Advance\model\AdvanceRequestModel;
use Advance\model\AdvanceSetupModel;
use Advance\Repository\AdvanceRequestRepository;
use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class AdvanceApply extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(AdvanceRequestRepository::class);
        $this->initializeForm(AdvanceRequestForm::class);
    }

    public function indexAction() {
        return $this->redirect()->toRoute("advanceStatus");
    }

    public function addAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);


            if ($this->form->isValid()) {
                $advanceRequestModel = new AdvanceRequestModel();
                $advanceRequestModel->exchangeArrayFromForm($this->form->getData());

                $advanceRequestModel->deductionType = $postData['deductionType'];
                $advanceRequestModel->advanceRequestId = (int) Helper::getMaxId($this->adapter, AdvanceRequestModel::TABLE_NAME, AdvanceRequestModel::ADVANCE_REQUEST_ID) + 1;
                $advanceRequestModel->status = "RQ";

                $this->repository->add($advanceRequestModel);
                $this->flashmessenger()->addMessage("Advance Request Successfully added!!!");

                return $this->redirect()->toRoute("advanceStatus");
            }
        }


        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'advance' => EntityHelper::getTableList($this->adapter, AdvanceSetupModel::TABLE_NAME, ['*'], [AdvanceSetupModel::STATUS => 'E']),
                    'customRenderer' => Helper::renderCustomView(),
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME, HrEmployees::SALARY], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"]),
        ]);
    }

}
