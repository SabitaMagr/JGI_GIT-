<?php

namespace Setup\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Setup\Form\AdvanceForm;
use Setup\Model\Advance;
use Setup\Model\Company;
use Setup\Repository\AdvanceRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class AdvanceController extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new AdvanceRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new AdvanceForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $list = $this->repository->fetchActiveRecord();
        return Helper::addFlashMessagesToArray($this, ['list' => $list]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $advanceModel = new Advance();
                $advanceModel->exchangeArrayFromForm($this->form->getData());
                $advanceModel->advanceId = ((int) Helper::getMaxId($this->adapter, Advance::TABLE_NAME, Advance::ADVANCE_ID)) + 1;
                $advanceModel->createdDate = Helper::getcurrentExpressionDate();
                $advanceModel->status = 'E';
                $advanceModel->createdBy = $this->employeeId;
                $this->repository->add($advanceModel);
                $this->flashmessenger()->addMessage("Advance Successfully added!!!");
                return $this->redirect()->toRoute('advance');
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC")
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('advance');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $advanceModel = new Advance();
        if (!$request->isPost()) {
            $advanceModel->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($advanceModel);
        } else {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $advanceModel->exchangeArrayFromForm($this->form->getData());
                $advanceModel->modifiedDate = Helper::getcurrentExpressionDate();
                $advanceModel->modifiedBy = $this->employeeId;
                $this->repository->edit($advanceModel, $id);
                $this->flashmessenger()->addMessage("Advance Successfully Updated!!!");
                return $this->redirect()->toRoute("advance");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC")
                        ]
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('advance');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Advance Successfully Deleted!!!");
        return $this->redirect()->toRoute('advance');
    }

}
