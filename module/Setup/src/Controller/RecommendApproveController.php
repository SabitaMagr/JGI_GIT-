<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/3/16
 * Time: 12:37 PM
 */
namespace Setup\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Setup\Model\RecommendApprove;
use Setup\Form\RecommendApproveForm;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Setup\Repository\RecommendApproveRepository;
use Zend\Form\Element\Select;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;

class RecommendApproveController extends AbstractActionController {
    private $form;
    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new RecommendApproveRepository($adapter);
    }
    public function indexAction()
    {
        $list = $this->repository->fetchAll();
        $recommendApproves = [];
        foreach($list  as $row){
            array_push($recommendApproves, $row);
        }
        return Helper::addFlashMessagesToArray($this, ['recommendApproves' => $recommendApproves]);
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $recommendApproveForm = new RecommendApproveForm();
        $this->form = $builder->createForm($recommendApproveForm);
    }
    public function addAction(){
        $request = $this->getRequest();
        $this->initializeForm();
        if($request->isPost()){
            $this->form->setData($request->getPost());
            
            if($this->form->isValid()){
                $recommendApprove = new RecommendApprove();
                $recommendApprove->exchangeArrayFromForm($this->form->getData());
                $recommendApprove->createdDt = Helper::getcurrentExpressionDate();
                $recommendApprove->status='E';
                $this->repository->add($recommendApprove);

                $this->flashmessenger()->addMessage("Recommender And Approver Successfully Assigned!!!");
                return $this->redirect()->toRoute("recommendapprove");
            }
        }

        return Helper::addFlashMessagesToArray($this,
            [
                'form' => $this->form,
                'employees' => $this->repository->getEmployees()
            ]
        );

    }
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute("id");
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
                $this->repository->edit($recommendApprove, $id);

                $this->flashmessenger()->addMessage("Recommender And Approver Successfully Assigned!!!");
                return $this->redirect()->toRoute("recommendapprove");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            //EntityHelper::getTableKVList($this->adapter,"HR_EMPLOYEES","EMPLOYEE_ID",["FIRST_NAME","MIDDLE_NAME","LAST_NAME"],["STATUS"=>"E"])
            'employees' => $this->repository->getEmployees($id)
        ]);
    }
    
    public function groupAssignAction(){
        $employeeNameFormElement = new Select();
        $employeeNameFormElement->setName("branch");
        $employeeName = \Application\Helper\EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"]," ");
        $employeeName[-1] = "All";
        ksort($employeeName);
        $employeeNameFormElement->setValueOptions($employeeName);
        $employeeNameFormElement->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeNameFormElement->setLabel("Employee");
        
        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches=\Application\Helper\EntityHelper::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME]);
        $branches[-1]="All";
        ksort($branches);
        $branchFormElement->setValueOptions($branches);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click","view()");


        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments=\Application\Helper\EntityHelper::getTableKVList($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME]);
        $departments[-1]="All";
        ksort($departments);
        $departmentFormElement->setValueOptions($departments);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations=\Application\Helper\EntityHelper::getTableKVList($this->adapter,Designation::TABLE_NAME,Designation::DESIGNATION_ID , [Designation::DESIGNATION_TITLE]);
        $designations[-1]="All";
        ksort($designations);
        $designationFormElement->setValueOptions($designations);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");
        
        return Helper::addFlashMessagesToArray($this, [
            "branches"=>$branchFormElement,
            "departments"=>$departmentFormElement,
            'designations'=>$designationFormElement,
            'employees'=>$employeeNameFormElement
            ]);
    }
}
