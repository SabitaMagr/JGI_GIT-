<?php

namespace Setup\Controller;

use Application\Helper\EntityHelper as EntityHelper1;
use Application\Helper\Helper;
use Setup\Form\JobHistoryForm;
use Setup\Model\JobHistory;
use Setup\Model\ServiceEventType;
use Setup\Repository\JobHistoryRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;

class JobHistoryController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new JobHistoryRepository($adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $jobHistoryForm = new JobHistoryForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($jobHistoryForm);
        }
    }

    public function indexAction() {

        $serviceEventTypeFormElement = new Select();
        $serviceEventTypeFormElement->setName("serviceEventType");
        $serviceEventTypes = \Application\Helper\EntityHelper::getTableKVListWithSortOption($this->adapter, ServiceEventType::TABLE_NAME, ServiceEventType::SERVICE_EVENT_TYPE_ID, [ServiceEventType::SERVICE_EVENT_TYPE_NAME], [ServiceEventType::STATUS => 'E'], "SERVICE_EVENT_TYPE_NAME", "ASC",null,false,true);
        $serviceEventTypes1 = [-1 => "All Service Event Type"] + $serviceEventTypes;
        $serviceEventTypeFormElement->setValueOptions($serviceEventTypes1);
        $serviceEventTypeFormElement->setAttributes(["id" => "serviceEventTypeId1", "class" => "form-control"]);
        $serviceEventTypeFormElement->setLabel("Service Event Type");
        
        $jobHistory = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, [
                    'jobHistoryList' => $jobHistory,
                    'serviceEventType' => $serviceEventTypeFormElement,
                    'searchValues' => EntityHelper1::getSearchData($this->adapter),
        ]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());
            // print_r($request->getPost()); die();

            if ($this->form->isValid()) {
                $jobHistory = new JobHistory();
                $jobHistory->exchangeArrayFromForm($this->form->getData());
                $jobHistory->jobHistoryId = ((int) Helper::getMaxId($this->adapter, JobHistory::TABLE_NAME, JobHistory::JOB_HISTORY_ID)) + 1;
                $jobHistory->startDate = Helper::getExpressionDate($jobHistory->startDate);
                $jobHistory->endDate = Helper::getExpressionDate($jobHistory->endDate);
                $jobHistory->createdDt = Helper::getcurrentExpressionDate();
                $jobHistory->createdBy = $this->employeeId;
                $jobHistory->status='E';
                $this->repository->add($jobHistory);
                $this->flashmessenger()->addMessage("Job History Successfully added!!!");
                return $this->redirect()->toRoute("jobHistory", ['action' => 'add']);
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages(),
                    'employees' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E","RETIRED_FLAG"=>"N"], "FIRST_NAME", "ASC", " ",false,true),
                    'departments' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'],"DEPARTMENT_NAME","ASC",null,true,true),
                    'designations' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_DESIGNATIONS", "DESIGNATION_ID", ["DESIGNATION_TITLE"], ["STATUS" => 'E'],"DESIGNATION_TITLE","ASC",null,true,true),
                    'branches' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_BRANCHES", "BRANCH_ID", ["BRANCH_NAME"], ["STATUS" => 'E'],"BRANCH_NAME","ASC",null,true,true),
                    'positions' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_POSITIONS", "POSITION_ID", ["POSITION_NAME"], ["STATUS" => 'E'],"POSITION_NAME","ASC",null,true,true),
                    'serviceTypes' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_SERVICE_TYPES", "SERVICE_TYPE_ID", ["SERVICE_TYPE_NAME"], ["STATUS" => 'E'],"SERVICE_TYPE_NAME","ASC",null,true,true),
                    'serviceEventTypes' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_SERVICE_EVENT_TYPES", "SERVICE_EVENT_TYPE_ID", ["SERVICE_EVENT_TYPE_NAME"], ["STATUS" => 'E'],"SERVICE_EVENT_TYPE_NAME","ASC",null,false,true)
                        ]
        );
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('jobHistory');
        }
        $this->initializeForm();
        $request = $this->getRequest();

        $jobHistory = new JobHistory();
        if (!$request->isPost()) {
            $jobHistoryDetail = $this->repository->fetchById($id);
            $employeeId = $jobHistoryDetail['EMPLOYEE_ID'];
            
            $getJobHistoryByEmployeeId = $this->repository->filter(null,null,$employeeId,-1);
            $empJobHistoryList = [];
            foreach($getJobHistoryByEmployeeId as $row){
                array_push($empJobHistoryList, $row);
            }
            if(count($empJobHistoryList)>=1){
                $latestJobHistoryId = $empJobHistoryList[0]['JOB_HISTORY_ID'];
            }else{
                $latestJobHistoryId=0;
            }         
            
            $jobHistory->exchangeArrayFromDb($jobHistoryDetail);
            $this->form->bind($jobHistory);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {

                $jobHistory->exchangeArrayFromForm($this->form->getData());

                $jobHistory->startDate = Helper::getExpressionDate($jobHistory->startDate);
                $jobHistory->endDate = Helper::getExpressionDate($jobHistory->endDate);
                $jobHistory->modifiedDt = Helper::getcurrentExpressionDate();
                $jobHistory->modifiedBy = $this->employeeId;

                $this->repository->edit($jobHistory, $id);
                $this->flashmessenger()->addMessage("Job History Successfully Updated!!!");
                return $this->redirect()->toRoute("jobHistory");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'latestJobHistoryId'=>$latestJobHistoryId,
                    'empId' => EntityHelper1::getTableKVList($this->adapter, JobHistory::TABLE_NAME, JobHistory::JOB_HISTORY_ID, [JobHistory::EMPLOYEE_ID], [JobHistory::JOB_HISTORY_ID => $id], null)[$id],
                    'messages' => $this->flashmessenger()->getMessages(),
                    'employeesAll' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => "E"], "FIRST_NAME", "ASC", " ",false,true),
                    'departments' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_DEPARTMENTS", "DEPARTMENT_ID", ["DEPARTMENT_NAME"], ["STATUS" => 'E'],"DEPARTMENT_NAME","ASC",null,true,true),
                    'designations' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_DESIGNATIONS", "DESIGNATION_ID", ["DESIGNATION_TITLE"], ["STATUS" => 'E'],"DESIGNATION_TITLE","ASC",null,true,true),
                    'branches' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_BRANCHES", "BRANCH_ID", ["BRANCH_NAME"], ["STATUS" => 'E'],"BRANCH_NAME","ASC",null,true,true),
                    'positions' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_POSITIONS", "POSITION_ID", ["POSITION_NAME"], ["STATUS" => 'E'],"POSITION_NAME","ASC",null,true,true),
                    'serviceTypes' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_SERVICE_TYPES", "SERVICE_TYPE_ID", ["SERVICE_TYPE_NAME"], ["STATUS" => 'E'],"SERVICE_TYPE_NAME","ASC",null,true,true),
                    'serviceEventTypes' => EntityHelper1::getTableKVListWithSortOption($this->adapter, "HRIS_SERVICE_EVENT_TYPES", "SERVICE_EVENT_TYPE_ID", ["SERVICE_EVENT_TYPE_NAME"], ["STATUS" => 'E'],"SERVICE_EVENT_TYPE_NAME","ASC",null,false,true)
                      ]
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('jobHistory');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Job History Successfully Deleted!!!");
        return $this->redirect()->toRoute("jobHistory");
    }

}
