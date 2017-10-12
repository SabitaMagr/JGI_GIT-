<?php

namespace LeaveManagement\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use LeaveManagement\Form\LeaveMasterForm;
use LeaveManagement\Model\LeaveMaster;
use LeaveManagement\Repository\LeaveMasterRepository;
use Setup\Model\Company;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LeaveSetup extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->repository = new LeaveMasterRepository($adapter);
        $this->adapter = $adapter;
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $leaveList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $leaveList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function initializeForm() {
        $leaveMasterForm = new LeaveMasterForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveMasterForm);
    }

    public function addAction() {
        ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this);
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveMaster = new LeaveMaster();
                $leaveMaster->exchangeArrayFromForm($this->form->getData());
                $leaveMaster->leaveId = ((int) Helper::getMaxId($this->adapter, LeaveMaster::TABLE_NAME, LeaveMaster::LEAVE_ID)) + 1;
                $leaveMaster->createdDt = Helper::getcurrentExpressionDate();
                $leaveMaster->createdBy = $this->employeeId;

                $leaveMaster->status = 'E';
                $this->repository->add($leaveMaster);
                $this->flashmessenger()->addMessage("Leave Successfully added!!!");
                return $this->redirect()->toRoute("leavesetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC", NULL, TRUE, TRUE),
                    'fiscalYears' => EntityHelper::getTableKVList($this->adapter, "HRIS_FISCAL_YEARS", "FISCAL_YEAR_ID", ["FISCAL_YEAR_NAME"], null)
                        ]
                )
        );
    }

    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("id");

        if ($id === 0) {
            return $this->redirect()->toRoute("shift");
        }

        $request = $this->getRequest();
        $leaveMaster = new LeaveMaster();
        if (!$request->isPost()) {
            $leaveMaster->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($leaveMaster);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveMaster->exchangeArrayFromForm($this->form->getData());
                $leaveMaster->modifiedDt = Helper::getcurrentExpressionDate();
                $leaveMaster->modifiedBy = $this->employeeId;

                $this->repository->edit($leaveMaster, $id);
                $this->flashmessenger()->addMessage("Leave Successfuly Updated!!!");
                return $this->redirect()->toRoute("leavesetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'customRenderer' => Helper::renderCustomView(),
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC", NULL, TRUE, TRUE),
                    'fiscalYears' => EntityHelper::getTableKVList($this->adapter, "HRIS_FISCAL_YEARS", "FISCAL_YEAR_ID", ["FISCAL_YEAR_NAME"], null)
                        ]
                )
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('leavesetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Leave Successfully Deleted!!!");
        return $this->redirect()->toRoute('leavesetup');
    }

}
