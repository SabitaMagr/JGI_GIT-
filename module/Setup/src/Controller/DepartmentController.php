<?php

namespace Setup\Controller;

use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Setup\Form\DepartmentForm;
use Setup\Helper\EntityHelper;
use Setup\Model\Department;
use Setup\Repository\DepartmentRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DepartmentController extends AbstractActionController {

    private $form;
    private $repository;
    private $adapter;
    private $employeeId;

    function __construct(AdapterInterface $adapter) {
        $this->repository = new DepartmentRepository($adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $departmentForm = new DepartmentForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($departmentForm);
        }
    }

    public function indexAction() {
        $departmentList = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['departments' => Helper::extractDbData($departmentList)]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $department = new Department();
                $department->exchangeArrayFromForm($this->form->getData());
                if ($department->parentDepartment == 0) {
                    unset($department->parentDepartment);
                }
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
                    'departments' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], ["STATUS" => "E"], "DEPARTMENT_NAME", "ASC"),
                    'countries' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HRIS_COUNTRIES)
                        ]
                )
        );
    }

    public function editAction() {

        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('department');
        }
        $this->initializeForm();
        $request = $this->getRequest();

        $department = new Department();
        if (!$request->isPost()) {
            $department->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($department);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $department->exchangeArrayFromForm($this->form->getData());
                if ($department->parentDepartment == 0) {
                    unset($department->parentDepartment);
                }
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
                    'departments' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], ["STATUS" => "E"], "DEPARTMENT_NAME", "ASC"),
                    'countries' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HRIS_COUNTRIES)
                        ]
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Department Successfully Deleted!!!");
        return $this->redirect()->toRoute('department');
    }

}

?>