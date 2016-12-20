<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/8/16
 * Time: 5:16 PM
 */

namespace LeaveManagement\Controller;


use Application\Helper\Helper;
use LeaveManagement\Form\LeaveApplyForm;
use LeaveManagement\Repository\LeaveApplyRepository;
use Setup\Repository\EmployeeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class LeaveApply extends AbstractActionController
{
    private $repository;
    private $form;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository=new LeaveApplyRepository($adapter);
        $this->adapter = $adapter;
    }
    public function initializeForm()
    {
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction()
    {
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeList = $employeeRepo->fetchAll();
        return Helper::addFlashMessagesToArray($this, [
            'employeeList' => $employeeList,
        ]);
    }

    public function addAction(){
        $this->initializeForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveApply = new \LeaveManagement\Model\LeaveApply();
                $leaveApply->exchangeArrayFromForm($this->form->getData());
                $leaveApply->requestedDt = Helper::getcurrentExpressionDate();
                $leaveApply->employeeId = $id;
                $leaveApply->status="RQ";
                $leaveApply->startDate=Helper::getExpressionDate($leaveApply->startDate);
                $leaveApply->endDate=Helper::getExpressionDate($leaveApply->endDate);

                $this->repository->add($leaveApply);
                $this->flashmessenger()->addMessage("Leave applied Successfully!!!");
                return $this->redirect()->toRoute("leaveapply");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'leaves'=> \Application\Helper\EntityHelper::getTableKVList($this->adapter, "HR_LEAVE_MASTER_SETUP", "LEAVE_ID", ["LEAVE_ENAME"],["STATUS"=>'E']),
            'employees' => \Application\Helper\EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"],["STATUS"=>'E']," "), 
            'customRenderer'=>Helper::renderCustomView()
        ]);

    }


}