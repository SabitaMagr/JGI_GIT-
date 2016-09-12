<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/8/16
 * Time: 5:17 PM
 */

namespace LeaveManagement\Controller;


use Application\Helper\Helper;
use LeaveManagement\Form\LeaveAssignForm;
use LeaveManagement\Helper\EntityHelper;
use LeaveManagement\Repository\LeaveAssignRepository;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class leaveAssign extends AbstractActionController
{
    private $repository;
    private $form;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new LeaveAssignRepository($adapter);
        $this->adapter = $adapter;
    }

    public function initializeForm()
    {
        $leaveAssignForm = new LeaveAssignForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveAssignForm);
    }

    public function indexAction()
    {
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeList = $employeeRepo->fetchAll();
        return Helper::addFlashMessagesToArray($this, [
            'employeeList' => $employeeList,
        ]);
    }

    public function assignAction()
    {
        $this->initializeForm();
        $id = (int)$this->params()->fromRoute("eid");

        if ($id === 0) {
            return $this->redirect()->toRoute("leaveassign");
        }
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employee = $employeeRepo->fetchById($id);

        $assignList = $this->repository->fetchByEmployeeId($id);
        return Helper::addFlashMessagesToArray($this, [
            'assignList' => $assignList,
            'id' => $id,
            'employee' => $employee
        ]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $id = (int)$this->params()->fromRoute("eid");

        if ($id === 0) {
            return $this->redirect()->toRoute("leaveassign");
        }

        $employeeRepo = new EmployeeRepository($this->adapter);
        $employee = $employeeRepo->fetchById($id);

        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveAssign = new \LeaveManagement\Model\LeaveAssign();
                $leaveAssign->exchangeArrayFromForm($this->form->getData());
                $leaveAssign->employeeLeaveAssignId = ((int)Helper::getMaxId($this->adapter, "HR_EMPLOYEE_LEAVE_ASSIGN", "EMPLOYEE_LEAVE_ASSIGN_ID")) + 1;
                $leaveAssign->createdDt = Helper::getcurrentExpressionDate();
                $leaveAssign->employeeId = $id;
                $this->repository->add($leaveAssign);
                $this->flashmessenger()->addMessage("Leave assigned Successfully!!!");
                return $this->redirect()->toRoute("leaveassign", ['action' => 'assign', 'eid' => $id]);
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'eid' => $id,
                'employee' => $employee,
                'leavelist' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_LEAVE_MASTER_SETUP)
            ]
        );
    }


    public function editAction()
    {
        $this->initializeForm();
        $eid = (int)$this->params()->fromRoute("eid");
        $id = (int)$this->params()->fromRoute("id");

        if ($id === 0 || $eid === 0) {
            return $this->redirect()->toRoute("leaveassign");
        }

        $request = $this->getRequest();
        $leaveAssign = new \LeaveManagement\Model\LeaveAssign();
        $employee = new HrEmployees();
        if (!$request->isPost()) {
            $leaveAssign->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($leaveAssign);
            $employee->employeeId = $leaveAssign->employeeId;
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveAssign->exchangeArrayFromForm($this->form->getData());
                $leaveAssign->modifiedDt = Helper::getcurrentExpressionDate();
                unset($leaveAssign->employeeLeaveAssignId);
                unset($leaveAssign->createdDt);
                $this->repository->edit($leaveAssign, $id);
                $this->flashmessenger()->addMessage("Assigned leave Successfuly Updated!!!");
                return $this->redirect()->toRoute("leaveassign", ['action' => 'assign', 'eid' => $eid]);

            }
        }
        return Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'id' => $id,
                'eid' => $eid,
                'employee' => $employee,
                'leavelist' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_LEAVE_MASTER_SETUP)
            ]
        );
    }

    public function deleteAction()
    {
        $eid = (int)$this->params()->fromRoute("eid");
        $id = (int)$this->params()->fromRoute("id");

        if ($id === 0 || $eid === 0) {
            return $this->redirect()->toRoute("leaveassign");
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Assigned Leave Successfully Deleted!!!");
        return $this->redirect()->toRoute("leaveassign", ['action' => 'assign', 'eid' => $eid]);
    }


}