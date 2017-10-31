<?php

namespace System\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use System\Form\UserSetupForm;
use System\Model\UserSetup;
use System\Repository\UserSetupRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class UserSetupController extends AbstractActionController {

    private $form;
    private $repository;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->repository = new UserSetupRepository($adapter);
        $this->adapter = $adapter;
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $roleSetupForm = new UserSetupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($roleSetupForm);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $list = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();
        $this->initializeForm();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $userSetup = new UserSetup();
                $userSetup->exchangeArrayFromForm($this->form->getData());
                $userSetup->userId = ((int) Helper::getMaxId($this->adapter, UserSetup::TABLE_NAME, UserSetup::USER_ID)) + 1;
                $userSetup->createdDt = Helper::getcurrentExpressionDate();
                $userSetup->createdBy = $this->employeeId;
                $userSetup->status = 'E';

                $userSetup->password = Helper::encryptPassword($userSetup->password);


                $this->repository->add($userSetup);

                $this->flashmessenger()->addMessage("User Successfully Added!!!");
                return $this->redirect()->toRoute("usersetup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeList' => $this->repository->getEmployeeList(),
                    'roleList' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_ROLES", "ROLE_ID", ["ROLE_NAME"], ["STATUS" => "E"], "ROLE_NAME", "ASC", null, false, true)
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $this->initializeForm();
        $request = $this->getRequest();

        $userSetup = new UserSetup();
        $detail = $this->repository->fetchById($id);
        if (!$request->isPost()) {
            $userSetup->exchangeArrayFromDB($detail);
            $this->form->bind($userSetup);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $userSetup->exchangeArrayFromForm($this->form->getData());
                $userSetup->modifiedDt = Helper::getcurrentExpressionDate();
                $userSetup->modifiedBy = $this->employeeId;
                unset($userSetup->createdDt);
                unset($userSetup->userId);
                unset($userSetup->status);

                $userSetup->password = Helper::encryptPassword($userSetup->password);

                $this->repository->edit($userSetup, $id);
                $this->flashmessenger()->addMessage("User Successfully Updated!!!");
                return $this->redirect()->toRoute("usersetup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'passwordDtl' => $detail['PASSWORD'],
                    'employeeList' => $this->repository->getEmployeeList($detail['EMPLOYEE_ID']),
                    'roleList' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_ROLES", "ROLE_ID", ["ROLE_NAME"], ["STATUS" => "E"], "ROLE_NAME", "ASC", null, false, true)
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('usersetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("User Successfully Deleted!!!");
        return $this->redirect()->toRoute('usersetup');
    }

}
