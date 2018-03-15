<?php

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\DesignationForm;
use Setup\Model\Company;
use Setup\Model\Designation;
use Setup\Repository\DesignationRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DesignationController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new DesignationRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $designationForm = new DesignationForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($designationForm);
        }
    }

    public function indexAction() {

        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $designationList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $designationList, 'error' => '']);
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
                $designation = new Designation();
                $designation->exchangeArrayFromForm($this->form->getData());
                $designation->createdDt = Helper::getcurrentExpressionDate();
                $designation->createdBy = $this->employeeId;
                $designation->designationId = ((int) Helper::getMaxId($this->adapter, "HRIS_DESIGNATIONS", "DESIGNATION_ID")) + 1;
                $designation->status = 'E';
                $this->repository->add($designation);

                $this->flashmessenger()->addMessage("Designation Successfully added!!!");
                return $this->redirect()->toRoute("designation");
            }
        }
        $CompanyWisedesignationList = $this->repository->fetchAllDesignationCompanyWise();
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'customRender' => Helper::renderCustomView(),
                    'designationListCompanyWise' => $CompanyWisedesignationList,
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC", null, true, true),
                    'messages' => $this->flashmessenger()->getMessages()
                        ]
                )
        );
    }

    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('designation');
        }
        $this->initializeForm();
        $request = $this->getRequest();
        $designation = new Designation();
        if (!$request->isPost()) {
            $fetchData = $this->repository->fetchById($id)->getArrayCopy();
            $designation->exchangeArrayFromDB($fetchData);
            $desginationId = $fetchData['DESIGNATION_ID'];
            $this->form->bind($designation);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {

                $designation->exchangeArrayFromForm($this->form->getData());
                $designation->modifiedDt = Helper::getcurrentExpressionDate();
                $designation->modifiedBy = $this->employeeId;
                $this->repository->edit($designation, $id);

                $this->flashmessenger()->addMessage("Designation Successfully Updated!!!");
                return $this->redirect()->toRoute("designation");
            }
        }
        $CompanyWisedesignationList = $this->repository->fetchAllDesignationCompanyWise();
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'customRender' => Helper::renderCustomView(),
                    'fetchedDesignationId' => $desginationId,
                    'designationListCompanyWise' => $CompanyWisedesignationList,
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC", null, true, true),
                    'messages' => $this->flashmessenger()->getMessages(),
                    'id' => $id
                ])
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('designation');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Designation Successfully Deleted!!!");
        return $this->redirect()->toRoute('designation');
    }

}
