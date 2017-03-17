<?php

namespace Setup\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Setup\Form\RecommendApproveForm;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\RecommendApprove;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class RecommendApproveController extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->repository = new RecommendApproveRepository($adapter);
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $list = $this->repository->fetchAll();
        $recommendApproves = [];
        foreach ($list as $row) {
            array_push($recommendApproves, $row);
        }
        return Helper::addFlashMessagesToArray($this, ['recommendApproves' => $recommendApproves]);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $recommendApproveForm = new RecommendApproveForm();
        $this->form = $builder->createForm($recommendApproveForm);
    }

    public function addAction() {
        $request = $this->getRequest();
        $this->initializeForm();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $recommendApprove = new RecommendApprove();
                $recommendApprove->exchangeArrayFromForm($this->form->getData());
                $recommendApprove->createdDt = Helper::getcurrentExpressionDate();
                $recommendApprove->createdBy = $this->employeeId;
                $recommendApprove->status = 'E';
                $this->repository->add($recommendApprove);

                $this->flashmessenger()->addMessage("Recommender And Approver Successfully Assigned!!!");
                return $this->redirect()->toRoute("recommendapprove");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employees' => $this->repository->getEmployees()
                        ]
        );
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $this->initializeForm();
        $request = $this->getRequest();

        $recommendApprove = new RecommendApprove();
        if (!$request->isPost()) {
            $recommendApprove->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($recommendApprove);
        } else {
            $modifiedDt = date('d-M-y');
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $recommendApprove->exchangeArrayFromForm($this->form->getData());
                $recommendApprove->modifiedDt = Helper::getcurrentExpressionDate();
                $recommendApprove->modifiedBy = $this->employeeId;
                $this->repository->edit($recommendApprove, $id);

                $this->flashmessenger()->addMessage("Recommender And Approver Successfully Assigned!!!");
                return $this->redirect()->toRoute("recommendapprove");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    //EntityHelper::getTableKVList($this->adapter,"HRIS_EMPLOYEES","EMPLOYEE_ID",["FIRST_NAME","MIDDLE_NAME","LAST_NAME"],["STATUS"=>"E"])
                    'employees' => $this->repository->getEmployees($id)
        ]);
    }

    public function groupAssignAction() {
        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
        $employeeName = EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E", "RETIRED_FLAG" => "N"], "FIRST_NAME", "ASC", " ");
        $employeeName1 = [-1 => "All"] + $employeeName;
        $employeeNameFormElement->setValueOptions($employeeName1);
        $employeeNameFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeNameFormElement->setLabel("Employee");
        $employeeNameFormElement->setAttribute("ng-click", "view()");

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], [Branch::STATUS => 'E'], "BRANCH_NAME", "ASC");
        $branches1 = [-1 => "All"] + $branches;
        $branchFormElement->setValueOptions($branches1);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click", "view()");

        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments = EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME], [Department::STATUS => 'E'], "DEPARTMENT_NAME", "ASC");
        $departments1 = [-1 => "All"] + $departments;
        $departmentFormElement->setValueOptions($departments1);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E'], "DESIGNATION_TITLE", "ASC");
        $designations1 = [-1 => "All"] + $designations;
        $designationFormElement->setValueOptions($designations1);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");

        return Helper::addFlashMessagesToArray($this, [
                    "branches" => $branchFormElement,
                    "departments" => $departmentFormElement,
                    'designations' => $designationFormElement,
                    'employees' => $employeeNameFormElement
        ]);
    }

}
