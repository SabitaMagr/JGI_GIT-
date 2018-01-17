<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Form\WagedEmployeeSetupForm;
use Customer\Model\WagedEmployeeSetupModel;
use Customer\Repository\WagedEmployeeSetupRepo;
use Exception;
use Setup\Model\Gender;
use Setup\Model\Zones;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class WagedEmployeeSetup extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(WagedEmployeeSetupRepo::class);
        $this->initializeForm(WagedEmployeeSetupForm::class);
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

        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);


            if ($this->form->isValid()) {
                $wagedEmployeeSetupModule = new WagedEmployeeSetupModel();
                $wagedEmployeeSetupModule->exchangeArrayFromForm($this->form->getData());

                $wagedEmployeeSetupModule->employeeId = (int) Helper::getMaxId($this->adapter, WagedEmployeeSetupModel::TABLE_NAME, WagedEmployeeSetupModel::EMPLOYEE_ID) + 1;

                $wagedEmployeeSetupModule->createdBy = $this->employeeId;
                $wagedEmployeeSetupModule->status = 'E';
                $wagedEmployeeSetupModule->fullName = $wagedEmployeeSetupModule->firstName . ' ' . $wagedEmployeeSetupModule->middleName . ' ' . $wagedEmployeeSetupModule->lastName;
                $this->repository->add($wagedEmployeeSetupModule);
                $this->flashmessenger()->addMessage("Successfully Added!!!");
                return $this->redirect()->toRoute("customer-waged-employee");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    "bloodGroups" => EntityHelper::getTableKVList($this->adapter, 'HRIS_BLOOD_GROUPS', 'BLOOD_GROUP_ID', ['BLOOD_GROUP_CODE'], NULL, NULL, TRUE),
                    "genders" => EntityHelper::getTableKVList($this->adapter, Gender::TABLE_NAME, Gender::GENDER_ID, [Gender::GENDER_NAME], null, null, true),
                    "zones" => EntityHelper::getTableKVList($this->adapter, Zones::TABLE_NAME, Zones::ZONE_ID, [Zones::ZONE_NAME], null, null, true),
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('advance-setup');
        }
        $request = $this->getRequest();

        $wagedEmployeeSetupModule = new WagedEmployeeSetupModel();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $wagedEmployeeSetupModule->exchangeArrayFromForm($this->form->getData());

                $wagedEmployeeSetupModule->fullName = $wagedEmployeeSetupModule->firstName . ' ' . $wagedEmployeeSetupModule->middleName . ' ' . $wagedEmployeeSetupModule->lastName;
                $wagedEmployeeSetupModule->modifiedDate = Helper::getcurrentExpressionDate();
                $wagedEmployeeSetupModule->modifiedBy = $this->employeeId;
                $this->repository->edit($wagedEmployeeSetupModule, $id);
                $this->flashmessenger()->addMessage("Successfully Updated!!!");
                return $this->redirect()->toRoute("customer-waged-employee");
            }
        }

        $wagedEmployeeSetupModule->exchangeArrayFromDB($this->repository->fetchById($id));
        $this->form->bind($wagedEmployeeSetupModule);
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'form' => $this->form,
                    "bloodGroups" => EntityHelper::getTableKVList($this->adapter, 'HRIS_BLOOD_GROUPS', 'BLOOD_GROUP_ID', ['BLOOD_GROUP_CODE'], NULL, NULL, TRUE),
                    "genders" => EntityHelper::getTableKVList($this->adapter, Gender::TABLE_NAME, Gender::GENDER_ID, [Gender::GENDER_NAME], null, null, true),
                    "zones" => EntityHelper::getTableKVList($this->adapter, Zones::TABLE_NAME, Zones::ZONE_ID, [Zones::ZONE_NAME], null, null, true),
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id == -1) {
            return $this->redirect()->toRoute('department');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage(" deleted successfully.");
        return $this->redirect()->toRoute("customer-waged-employee");
    }

}
