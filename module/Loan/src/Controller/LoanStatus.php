<?php
namespace Loan\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Loan\Repository\LoanStatusRepository;
use ManagerService\Repository\LoanApproveRepository;
use SelfService\Form\LoanRequestForm;
use SelfService\Model\LoanRequest;
use SelfService\Repository\LoanRequestRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Loan;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\RecommendApproveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class LoanStatus extends AbstractActionController
{
    private $adapter;
    private $loanApproveRepository;
    private $loanStatusRepository;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->loanApproveRepository = new LoanApproveRepository($adapter);
        $this->loanStatusRepository = new LoanStatusRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId =  $auth->getStorage()->read()['employee_id'];       
    }
    
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new LoanRequestForm();
        $this->form = $builder->createForm($form);
    }

    
    public function indexAction() {
        $loanFormElement = new Select();
        $loanFormElement->setName("loan");
        $loans = EntityHelper::getTableKVListWithSortOption($this->adapter, Loan::TABLE_NAME, Loan::LOAN_ID, [Loan::LOAN_NAME], [Loan::STATUS => 'E'], Loan::LOAN_NAME, "ASC",NULL,FALSE,TRUE);
        $loans1 = [-1 => "All Loans"] + $loans;
        $loanFormElement->setValueOptions($loans1);
        $loanFormElement->setAttributes(["id" => "loanId", "class" => "form-control"]);
        $loanFormElement->setLabel("Loan Type");

        $loanStatus = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected',
            'C'=>'Cancelled'
        ];
        $loanStatusFormElement = new Select();
        $loanStatusFormElement->setName("loanStatus");
        $loanStatusFormElement->setValueOptions($loanStatus);
        $loanStatusFormElement->setAttributes(["id" => "loanRequestStatusId", "class" => "form-control"]);
        $loanStatusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'loans' => $loanFormElement,
                    'loanStatus' => $loanStatusFormElement,
                    'searchValues'=> EntityHelper::getSearchData($this->adapter)
        ]);
    }
    public function viewAction() {
        $this->initializeForm();
        $loanRequestRepository = new LoanRequestRepository($this->adapter);
        $loanApproveRepository = new LoanApproveRepository($this->adapter);

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("loanStatus");
        }
        $loanRequest = new LoanRequest();
        $request = $this->getRequest();

        $detail = $this->loanApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $employeeId = $detail['EMPLOYEE_ID'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);
        $recommApprove = 0;
        if($empRecommendApprove['RECOMMEND_BY']==$empRecommendApprove['APPROVED_BY']){
            $recommApprove=1;
        }
        
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };
        
        $employeeName = $fullName($detail['EMPLOYEE_ID']);        
        $authRecommender = ($status=='RQ' || $status=='C')? $detail['RECOMMENDER'] : $detail['RECOMMENDED_BY'];
        $authApprover = ($status=='RC' || $status=='C' || $status=='RQ' || ($status=='R' && $approvedDT==null))? $detail['APPROVER'] : $detail['APPROVED_BY'];

        if (!$request->isPost()) {
            $loanRequest->exchangeArrayFromDB($detail);
            $this->form->bind($loanRequest);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $loanRequest->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $loanRequest->status = "R";
                $this->flashmessenger()->addMessage("Loan Request Rejected!!!");
            } else if ($action == "Approve") {
                $loanRequest->status = "AP";
                $this->flashmessenger()->addMessage("Loan Request Approved");
            }
            $loanRequest->approvedBy = $this->employeeId;
            $loanRequest->approvedRemarks = $reason;
            $this->loanApproveRepository->edit($loanRequest, $id);

            return $this->redirect()->toRoute("loanStatus");
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeId' => $employeeId,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DATE'],
                    'recommender' => $fullName($authRecommender),
                    'approver' => $fullName($authApprover),
                    'approvedDT'=>$detail['APPROVED_DATE'],
                    'status' => $status,
                    'loans' => EntityHelper::getTableKVListWithSortOption($this->adapter, Loan::TABLE_NAME, Loan::LOAN_ID, [Loan::LOAN_NAME], [Loan::STATUS => "E"], Loan::LOAN_ID, "ASC",NULL,FALSE,TRUE),
                    'customRenderer' => Helper::renderCustomView(),
                    'recommApprove'=>$recommApprove
        ]);
    }
    
}