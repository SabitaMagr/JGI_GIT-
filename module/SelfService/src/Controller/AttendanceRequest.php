<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/15/16
 * Time: 1:20 PM
 */
namespace SelfService\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Form\AttendanceRequestForm;
use SelfService\Repository\AttendanceRequestRepository;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Setup\Helper\EntityHelper;
use SelfService\Model\AttendanceRequestModel;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;

class AttendanceRequest extends  AbstractActionController
{
    private $adapter;
    private $repository;
    private $form;
    private $employeeId;
    private $authService;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new AttendanceRequestRepository($adapter);

        $this->authService =new AuthenticationService();
        $detail = $this->authService->getIdentity();
        $this->employeeId=$detail['employee_id'];
    }

    public function initializeForm()
    {
        $builder = new AnnotationBuilder();
        $attendanceRequest = new AttendanceRequestForm();
        $this->form = $builder->createForm($attendanceRequest);
    }

    public function indexAction()
    {
        $attendanceList = $this->repository->fetchByEmpId($this->employeeId);
        return Helper::addFlashMessagesToArray($this, ['attendanceList' => $attendanceList]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();

        $recommendApproveRepository =  new RecommendApproveRepository($this->adapter);
        $empRecommendApprove  = $recommendApproveRepository->fetchById($this->employeeId);

        if($empRecommendApprove!=null){
            $approvedBy = $empRecommendApprove['RECOMMEND_BY'];
        }else{
            $result = $this->approverList();
            //$recommendBy=$result['recommender'][0]['id'];
            $approvedBy=$result['approver'][0]['id'];
        }

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model = new AttendanceRequestModel();
                $model->exchangeArrayFromForm($this->form->getData());
                $model->employeeId = $this->employeeId;
                $model->attendanceDt = Helper::getExpressionDate($model->attendanceDt);
                $model->id = ((int)Helper::getMaxId($this->adapter, $model::TABLE_NAME, "ID")) + 1;
                $model->inTime = Helper::getExpressionTime($model->inTime);
                $model->outTime = Helper::getExpressionTime($model->outTime);
                $model->status ="RQ";
                $model->approvedBy=$approvedBy;

                $this->repository->add($model);
                $this->flashmessenger()->addMessage("Attendance Request Submitted Successfully!!");
                return $this->redirect()->toRoute("attendancerequest");
            }
        }
        return Helper::addFlashMessagesToArray($this,
            [
                'form' => $this->form
            ]
        );
    }

    public function editAction()
    {
        $this->initializeForm();
        $id = (int)$this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("attendancerequest");
        }

        $request = $this->getRequest();
        $model = new AttendanceRequestModel();
        if (!$request->isPost()) {
            $model->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($model);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->attendanceDt = Helper::getExpressionDate($model->attendanceDt);
                $model->inTime = Helper::getExpressionTime($model->inTime);
                $model->outTime = Helper::getExpressionTime($model->outTime);
                $this->repository->edit($model, $id);
                $this->flashmessenger()->addMessage("Attendance Request Updated Successfully!!");
                return $this->redirect()->toRoute("attendancerequest");
            }
        }
        return Helper::addFlashMessagesToArray($this,
            [
                'form' => $this->form,
                'id' => $id,
            ]
        );
    }

    public function deleteAction()
    {

    }
    public function approverList(){
        $employeeRepository = new EmployeeRepository($this->adapter);
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $employeeId = $this->employeeId;
        $employeeDetail = $employeeRepository->fetchById($employeeId);
        $branchId = $employeeDetail['BRANCH_ID'];
        $departmentId = $employeeDetail['DEPARTMENT_ID'];
        $designations =$recommendApproveRepository->getDesignationList($employeeId);

        $approver = array();
        foreach($designations as $key=>$designationList){
            $withinBranch = $designationList['WITHIN_BRANCH'];
            $withinDepartment = $designationList['WITHIN_DEPARTMENT'];
            $designationId = $designationList['DESIGNATION_ID'];
            $employees = $recommendApproveRepository->getEmployeeList($withinBranch,$withinDepartment,$designationId,$branchId,$departmentId);

            if($key==1){
                $i=0;
                foreach($employees as $employeeList){
                    // array_push($recommender,$employeeList);
                    $approver [$i]["id"]=$employeeList['EMPLOYEE_ID'];
                    $approver [$i]["name"]= $employeeList['FIRST_NAME']." ".$employeeList['MIDDLE_NAME']." ".$employeeList['LAST_NAME'];
                    $i++;
                }
            }
        }
        $responseData = [
            "approver" => $approver,
        ];
        return $responseData;
    }
}