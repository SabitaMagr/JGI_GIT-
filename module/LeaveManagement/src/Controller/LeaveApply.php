<?php

namespace LeaveManagement\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use LeaveManagement\Form\LeaveApplyForm;
use LeaveManagement\Model\LeaveApply as LeaveApplyModel;
use LeaveManagement\Repository\LeaveApplyRepository;
use Setup\Repository\EmployeeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class LeaveApply extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new LeaveApplyRepository($adapter);
        $this->adapter = $adapter;
    }

    public function initializeForm() {
        $leaveApplyForm = new LeaveApplyForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction() {
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeList = $employeeRepo->fetchAll();
        return Helper::addFlashMessagesToArray($this, [
                    'employeeList' => $employeeList,
        ]);
    }

    public function addAction() {
        $this->initializeForm();
        $basePath = $this->getRequest()->getBaseUrl('uploads/logo2.gif');
        $url = $this->getActionController()->view()->baseUrl();
        
        print_r($this->getRequest()->getBaseUrl('uploads/logo2.gif')); die();
        $htmlDescription = "<img src='' height='50' width='50 id=''/>";
        
//        print_r($basePath); 
        print_r($basePath); die();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveApply = new LeaveApplyModel();
                $leaveApply->exchangeArrayFromForm($this->form->getData());
                $leaveApply->requestedDt = Helper::getcurrentExpressionDate();
                $leaveApply->employeeId = $id;
                $leaveApply->status = "RQ";
                $leaveApply->startDate = Helper::getExpressionDate($leaveApply->startDate);
                $leaveApply->endDate = Helper::getExpressionDate($leaveApply->endDate);

                $this->repository->add($leaveApply);
                $this->flashmessenger()->addMessage("Leave applied Successfully!!!");
                return $this->redirect()->toRoute("leaveapply");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'leaves' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_LEAVE_MASTER_SETUP", "LEAVE_ID", ["LEAVE_ENAME"], ["STATUS" => 'E'], "LEAVE_ENAME", "ASC", NULL, FALSE, TRUE),
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ", FALSE, TRUE),
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }

}
