<?php

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\DepartmentForm;
use Setup\Helper\EntityHelper;
use Setup\Model\Company;
use Setup\Model\Department;
use Setup\Repository\DepartmentRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DepartmentController extends AbstractActionController {

    private $form;
    private $repository;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->repository = new DepartmentRepository($adapter);
        $this->adapter = $adapter;
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm() {
        $departmentForm = new DepartmentForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($departmentForm);
        }
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $departmentList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $departmentList, 'error' => '']);
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
                $department = new Department();
                $department->exchangeArrayFromForm($this->form->getData());
                $department->createdDt = Helper::getcurrentExpressionDate();
                $department->createdBy = $this->employeeId;
                $department->departmentId = ((int) Helper::getMaxId($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID")) + 1;
                $department->status = 'E';
                $this->repository->add($department);
                $this->flashmessenger()->addMessage("Department Successfully added!!!");
                return $this->redirect()->toRoute("department");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'departments' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], ["STATUS" => "E"], "DEPARTMENT_NAME", "ASC", null, false, true),
                    'company' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], "COMPANY_NAME", "ASC", null, true, true),
                    'branch' => $this->repository->fetchAllBranchAndCompany(),
                    'countries' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HRIS_COUNTRIES)
                        ]
        ));
    }

    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->
                            toRoute('department');
        }
        $this->initializeForm();
        $request = $this->getRequest();

        $department = new Department();
        $detail = $this->repository->fetchById($id)->getArrayCopy();
        if (!$request->isPost()) {
            $department->exchangeArrayFromDb($detail);
            $this->form->bind($department);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $department->exchangeArrayFromForm($this->form->getData());
                $department->modifiedDt = Helper::getcurrentExpressionDate();
                $department->modifiedBy = $this->employeeId;

                $this->repository->edit($department, $id);
                $this->flashmessenger()->addMessage("Department Successfully Updated!!!");
                return $this->redirect()->toRoute("department");
            }
        }

        return Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form, 'id' => $id,
                    'departments' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], ["STATUS" => "E"], "DEPARTMENT_NAME", "ASC", null, false, true),
                    'company' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], "COMPANY_NAME", "ASC", null, true, true),
                    'branch' => $this->repository->fetchAllBranchAndCompany(),
                    'countries' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HRIS_COUNTRIES),
                        ]
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('department');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Department Successfully Deleted!!!");
        return $this->redirect()->toRoute('department');
    }

}

?>