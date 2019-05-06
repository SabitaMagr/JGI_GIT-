<?php
namespace Loan\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Form\LoanRequestForm;
use Loan\Form\LoanCLosing AS LoanCLosingForm;
use Setup\Model\HrEmployees;
use Loan\Model\LoanClosing AS LoanClosingModel;
use SelfService\Repository\LoanRequestRepository;
use Loan\Repository\LoanClosingRepository;
use SelfService\Model\LoanRequest as LoanRequestModel;
use Setup\Model\Loan;
use ManagerService\Repository\LoanApproveRepository;

class LoanApply extends AbstractActionController{
    private $form;
    private $loanCLosingForm;
    private $adapter;
    private $loanRequesteRepository;
    private $loanClosingRepository;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->loanRequesteRepository = new LoanRequestRepository($adapter);
        $this->loanClosingRepository = new LoanClosingRepository($adapter);
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new LoanRequestForm();
        $this->form = $builder->createForm($form);
    }
    public function initializeClosingForm(){
        $builder = new AnnotationBuilder();
        $loanCLosingForm = new LoanCLosingForm();
        $this->loanCLosingForm = $builder->createForm($loanCLosingForm);
    }
    
    public function indexAction() {
       return $this->redirect()->toRoute("loanStatus");
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
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $model->deductOnSalary = 'Y';
                $this->loanRequesteRepository->add($model);
                $this->flashmessenger()->addMessage("Loan Request Successfully added!!!");
                return $this->redirect()->toRoute("loanStatus");
            }
        }
        
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employees'=> EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"],["STATUS"=>'E','RETIRED_FLAG'=>'N'],"FIRST_NAME","ASC"," ",FALSE,TRUE),
                    'loans' => EntityHelper::getTableKVListWithSortOption($this->adapter, Loan::TABLE_NAME, Loan::LOAN_ID, [Loan::LOAN_NAME], [Loan::STATUS => "E"], Loan::LOAN_ID, "ASC",NULL,FALSE,TRUE)
        ]);
    }

    public function issueNewLoanAsRequired($emp_id, $paymentAmount, $old_loan_req_id, $repaymentMonths){
        $loanAmount = $this->loanClosingRepository->getRemainingAmount($old_loan_req_id, $paymentAmount);
        $loanAmount = Helper::extractDbData($loanAmount)[0]['REMAINING_AMOUNT'];
        $oldLoanId = $this->loanClosingRepository->getOldLoanId($old_loan_req_id);
        $oldLoanId = Helper::extractDbData($oldLoanId)[0]['LOAN_ID'];
        
        $model = new LoanRequestModel();
        $model->loanRequestId = ((int) Helper::getMaxId($this->adapter, LoanRequestModel::TABLE_NAME, LoanRequestModel::LOAN_REQUEST_ID)) + 1;
        $model->requestedDate = Helper::getcurrentExpressionDate();
        $model->loanDate = Helper::getcurrentExpressionDate();
        $model->status = 'RQ';
        $model->employeeId = $emp_id;
        $model->repaymentMonths = $repaymentMonths;
        $model->requestedAmount = $loanAmount;
        $model->reason = '';
        $model->loanId = $oldLoanId;
        $model->deductOnSalary = 'Y';
        $this->loanRequesteRepository->add($model);
        $newLoanReqId = ((int) Helper::getMaxId($this->adapter, LoanRequestModel::TABLE_NAME, LoanRequestModel::LOAN_REQUEST_ID));
        $model = new LoanClosingModel();
        $loanApproveRepository = new LoanApproveRepository($this->adapter);
        $loanRequest = new LoanRequestModel();
        $model->newLoanReqId = $newLoanReqId;
        $this->loanClosingRepository->edit($model, $old_loan_req_id);
        $loanRequest->status = "AP";
        $loanApproveRepository->addToDetails($newLoanReqId);
        $loanRequest->approvedBy = $this->employeeId;
        $loanRequest->approvedRemarks = '';
        $loanApproveRepository->edit($loanRequest, $newLoanReqId);
        $this->flashmessenger()->addMessage("Loan Successfully closed with this payment 
        and new Loan has been issued and approved!!!");
        return $this->redirect()->toRoute("loanStatus");
    }

    public function loanClosingAction() {
        $this->initializeClosingForm();
        $request = $this->getRequest();
        $model = new LoanClosingModel();
        $id = (int) $this->params()->fromRoute('id');
        if ($request->isPost()) {
            $this->loanCLosingForm->setData($request->getPost());
            if ($this->loanCLosingForm->isValid()) {
                $model->exchangeArrayFromForm($this->loanCLosingForm->getData());
                $model->id = ((int) Helper::getMaxId($this->adapter, LoanClosingModel::TABLE_NAME, LoanClosingModel::ID)) + 1;
                $model->paymentDate = Helper::getcurrentExpressionDate();
                $model->loanReqId = (int) $this->params()->fromRoute('id');
                $this->loanClosingRepository->add($model);
                $this->loanClosingRepository->updateLoanStatus($model->loanReqId);

                $emp_id = $this->loanClosingRepository->getEmployeeByLoanRequestId($id);
                $emp_id = Helper::extractDbData($emp_id)[0]['EMPLOYEE_ID'];
                if(!empty($_POST['repaymentMonths'])){
                    $this->issueNewLoanAsRequired($emp_id, $model->paymentAmount, $id, $_POST['repaymentMonths']);
                }
                else{
                    $this->flashmessenger()->addMessage("Loan Successfully closed with this payment!!!");
                }
                return $this->redirect()->toRoute("loanStatus");
            }
        }  
        $emp_id = $this->loanClosingRepository->getEmployeeByLoanRequestId($id);
        $emp_id = Helper::extractDbData($emp_id)[0]['EMPLOYEE_ID'];
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->loanCLosingForm,
            'id' => $id,
            'employee'=> EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"],["EMPLOYEE_ID"=>$emp_id,"STATUS"=>'E','RETIRED_FLAG'=>'N'],"FIRST_NAME","ASC"," ",FALSE,TRUE),
            'unpaidAmount'=>Helper::extractDbData($this->loanClosingRepository->getUnpaidAmount($id))[0]['UNPAID_AMOUNT']
        ]);
    }
}