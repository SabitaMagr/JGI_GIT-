<?php

namespace SelfService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Form\LoanRequestForm;
use Setup\Model\HrEmployees;
use SelfService\Model\LoanRequest as LoanRequestModel;
use SelfService\Repository\LoanRequestRepository;
use Setup\Model\Loan;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;

class LoanRequest extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;
    private $employeeId;
    private $recommender;
    private $approver;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new LoanRequestRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new LoanRequestForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $result = $this->repository->getAllByEmployeeId($this->employeeId);
        $list = [];
        $getValue = function($status) {
            if ($status == "RQ") {
                return "Pending";
            } else if ($status == 'RC') {
                return "Recommended";
            } else if ($status == "R") {
                return "Rejected";
            } else if ($status == "AP") {
                return "Approved";
            } else if ($status == "C") {
                return "Cancelled";
            }
        };
        $getAction = function($status) {
            if ($status == "RQ") {
                return ["delete" => 'Cancel Request'];
            } else {
                return ["view" => 'View'];
            }
        };
        foreach ($result as $row) {
            $status = $getValue($row['STATUS']);
            $action = $getAction($row['STATUS']);
            $new_row = array_merge($row, ['STATUS' => $status, 'ACTION' => key($action), 'ACTION_TEXT' => $action[key($action)]]);
            array_push($list, $new_row);
        }
        return Helper::addFlashMessagesToArray($this, ['list' => $list]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new LoanRequestModel();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->loanRequestId = ((int) Helper::getMaxId($this->adapter, LoanRequestModel::TABLE_NAME, LoanRequestModel::LOAN_REQUEST_ID)) + 1;
                $model->employeeId = $this->employeeId;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $model->deductOnSalary = 'Y';
                $this->repository->add($model);
                $this->flashmessenger()->addMessage("Loan Request Successfully added!!!");
                return $this->redirect()->toRoute("loanRequest");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'loans' => EntityHelper::getTableKVListWithSortOption($this->adapter, Loan::TABLE_NAME, Loan::LOAN_ID, [Loan::LOAN_NAME], [Loan::STATUS => "E"], Loan::LOAN_ID, "ASC")
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('loanRequest');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Loan Request Successfully Cancelled!!!");
        return $this->redirect()->toRoute('loanRequest');
    }
    public function getRecommendApprover(){
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($this->employeeId);

        if ($empRecommendApprove != null) {
            $this->recommender = $empRecommendApprove['RECOMMEND_BY'];
            $this->approver = $empRecommendApprove['APPROVED_BY'];
        } else {
            $result = $this->recommendApproveList();
            if(count($result['recommender'])>0){
                $this->recommender=$result['recommender'][0]['id'];
            }else{
                $this->recommender=null;
            }
            if(count($result['approver'])>0){
                $this->approver=$result['approver'][0]['id'];
            }else{
                 $this->approver=null;
            } 
        }
    }

    public function viewAction() {
        $this->initializeForm();
        $this->getRecommendApprover();
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("loanRequest");
        }
        $fullName = function($id){
          $empRepository = new EmployeeRepository($this->adapter);
          $empDtl = $empRepository->fetchById($id);
          $empMiddleName = ($empDtl['MIDDLE_NAME']!=null)? " ".$empDtl['MIDDLE_NAME']." " :" ";
          return $empDtl['FIRST_NAME'].$empMiddleName.$empDtl['LAST_NAME'];
        };
        
        $recommenderName = $fullName($this->recommender);
        $approverName = $fullName($this->approver);
        
        $model = new LoanRequestModel();
        $detail = $this->repository->fetchById($id);
        $status = $detail['STATUS'];
        $approvedDT = $detail['APPROVED_DATE'];
        $MN1 = ($detail['MN1']!=null)? " ".$detail['MN1']." ":" ";
        $recommended_by = $detail['FN1'].$MN1.$detail['LN1'];        
        $MN2 = ($detail['MN2']!=null)? " ".$detail['MN2']." ":" ";
        $approved_by = $detail['FN2'].$MN2.$detail['LN2'];
        $authRecommender = ($status=='RQ' || $status=='C')?$recommenderName:$recommended_by;
        $authApprover = ($status=='RC' || $status=='RQ' || $status=='C' || ($status=='R' && $approvedDT==null))?$approverName:$approved_by;
       
        $model->exchangeArrayFromDB($detail);
        $this->form->bind($model);
                       
        $middleName = ($detail['MIDDLE_NAME']!=null)? " ".$detail['MIDDLE_NAME']." " :" ";
        $employeeName = $detail['FIRST_NAME'].$middleName.$detail['LAST_NAME'];

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employeeName'=>$employeeName,
                    'status'=>$detail['STATUS'],
                    'requestedDate'=>$detail['REQUESTED_DATE'],
                    'recommender'=>$authRecommender,
                    'approver'=>$authApprover,
                    'loans' => EntityHelper::getTableKVListWithSortOption($this->adapter, Loan::TABLE_NAME, Loan::LOAN_ID, [Loan::LOAN_NAME], [Loan::STATUS => "E"], Loan::LOAN_ID, "ASC")
        ]);       
    }
    public function recommendApproveList() {
        $employeeRepository = new EmployeeRepository($this->adapter);
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $employeeId = $this->employeeId;
        $employeeDetail = $employeeRepository->fetchById($employeeId);
        $branchId = $employeeDetail['BRANCH_ID'];
        $departmentId = $employeeDetail['DEPARTMENT_ID'];
        $designations = $recommendApproveRepository->getDesignationList($employeeId);

        $recommender = array();
        $approver = array();
        foreach ($designations as $key => $designationList) {
            $withinBranch = $designationList['WITHIN_BRANCH'];
            $withinDepartment = $designationList['WITHIN_DEPARTMENT'];
            $designationId = $designationList['DESIGNATION_ID'];
            $employees = $recommendApproveRepository->getEmployeeList($withinBranch, $withinDepartment, $designationId, $branchId, $departmentId);

            if ($key == 1) {
                $i = 0;
                foreach ($employees as $employeeList) {
                    // array_push($recommender,$employeeList);
                    $recommender [$i]["id"] = $employeeList['EMPLOYEE_ID'];
                    $recommender [$i]["name"] = $employeeList['FIRST_NAME'] . " " . $employeeList['MIDDLE_NAME'] . " " . $employeeList['LAST_NAME'];
                    $i++;
                }
            } else if ($key == 2) {
                $i = 0;
                foreach ($employees as $employeeList) {
                    //array_push($approver,$employeeList);
                    $approver [$i]["id"] = $employeeList['EMPLOYEE_ID'];
                    $approver [$i]["name"] = $employeeList['FIRST_NAME'] . " " . $employeeList['MIDDLE_NAME'] . " " . $employeeList['LAST_NAME'];
                    $i++;
                }
            }
        }
        $responseData = [
            "recommender" => $recommender,
            "approver" => $approver
        ];
        return $responseData;
    }
}
