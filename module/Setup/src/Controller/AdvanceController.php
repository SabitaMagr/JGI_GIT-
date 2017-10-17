<?php

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\AdvanceForm;
use Setup\Model\Advance;
use Setup\Model\Company;
use Setup\Repository\AdvanceRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class AdvanceController extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new AdvanceRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new AdvanceForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchActiveRecord();
                $advanceList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $advanceList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function addAction() {
        ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this);
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
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC", null, false, true)
        ]);
    }

    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
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
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC", null, false, true)
                        ]
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('advance');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Advance Successfully Deleted!!!");
        return $this->redirect()->toRoute('advance');
    }

}
