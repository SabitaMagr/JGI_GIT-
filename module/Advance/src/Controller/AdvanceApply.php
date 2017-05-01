<?php

namespace Advance\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use SelfService\Form\AdvanceRequestForm;
use SelfService\Model\AdvanceRequest as AdvanceRequestModel;
use SelfService\Repository\AdvanceRequestRepository;
use Setup\Model\Advance;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class AdvanceApply extends AbstractActionController {

    private $form;
    private $adapter;
    private $advanceRequesteRepository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->advanceRequesteRepository = new AdvanceRequestRepository($adapter);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new AdvanceRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        return $this->redirect()->toRoute("advanceStatus");
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new AdvanceRequestModel();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->advanceRequestId = ((int) Helper::getMaxId($this->adapter, AdvanceRequestModel::TABLE_NAME, AdvanceRequestModel::ADVANCE_REQUEST_ID)) + 1;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $model->deductOnSalary = 'Y';
                $this->advanceRequesteRepository->add($model);
                $this->flashmessenger()->addMessage("Advance Request Successfully added!!!");
                return $this->redirect()->toRoute("advanceStatus");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ",FALSE,TRUE),
                    'advances' => EntityHelper::getTableKVListWithSortOption($this->adapter, Advance::TABLE_NAME, Advance::ADVANCE_ID, [Advance::ADVANCE_NAME], [Advance::STATUS => "E"], Advance::ADVANCE_ID, "ASC",NULL,FALSE,TRUE)
        ]);
    }

}
