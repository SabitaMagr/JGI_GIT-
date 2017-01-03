<?php
namespace Setup\Controller;

use Application\Helper\Helper;
use Setup\Form\LoanForm;
use Setup\Model\Loan;
use Setup\Repository\LoanRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class LoanController extends AbstractActionController{
    private $form;
    private $adapter;
    private $repository;
    private $employeeId;
        
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $this->repository = new LoanRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new LoanForm();
        $this->form = $builder->createForm($form);
    }
    
    public function indexAction(){
        $list = $this->repository->fetchActiveRecord();
        return Helper::addFlashMessagesToArray($this, ['list'=>$list]);       
    } 
    
    public function addAction(){
        $this->initializeForm();
        $request = $this->getRequest();   
       
        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = EntityHelper2::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => 'E'], "DESIGNATION_TITLE", "ASC");
        $designations1 = [-1 => "All"] + $designations;
        $designationFormElement->setValueOptions($designations1);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control","multiple"=>"multiple"]);
        $designationFormElement->setLabel("Designation List");

        $positionFormElement = new Select();
        $positionFormElement->setName("position");
        $positions = EntityHelper2::getTableKVListWithSortOption($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME], [Position::STATUS => 'E'], "POSITION_NAME", "ASC");
        $positions1 = [-1 => "All"] + $positions;
        $positionFormElement->setValueOptions($positions1);
        $positionFormElement->setAttributes(["id" => "positionId", "class" => "form-control","multiple"=>"multiple"]);
        $positionFormElement->setLabel("Position List");

        $serviceTypeFormElement = new Select();
        $serviceTypeFormElement->setName("serviceType");
        $serviceTypes = EntityHelper2::getTableKVListWithSortOption($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => 'E'], "SERVICE_TYPE_NAME", "ASC");
        $serviceTypes1 = [-1 => "All"] + $serviceTypes;
        $serviceTypeFormElement->setValueOptions($serviceTypes1);
        $serviceTypeFormElement->setAttributes(["id" => "serviceTypeId", "class" => "form-control","multiple"=>"multiple"]);
        $serviceTypeFormElement->setLabel("Service Type List");
        
        if($request->isPost()){
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
               $loanModel = new Loan();   
               $loanModel->exchangeArrayFromForm($this->form->getData());
               $loanModel->loanId = ((int) Helper::getMaxId($this->adapter, Loan::TABLE_NAME, Loan::LOAN_ID))+1;
               $loanModel->createdDate = Helper::getcurrentExpressionDate();
               $loanModel->status = 'E';
               $loanModel->createdBy = $this->employeeId;
               $this->repository->add($loanModel);
               $this->flashmessenger()->addMessage("Loan Successfully added!!!");
               return $this->redirect()->toRoute('loan');
            }       
        }
        return Helper::addFlashMessagesToArray($this, [
            'form'=>$this->form,
            'designation'=>$designationFormElement,
            'position'=>$positionFormElement,
            'serviceType'=>$serviceTypeFormElement
                ]);              
    }
    
    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('loan');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $loanModel = new Loan();
        if (!$request->isPost()) {
            $loanModel->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($loanModel);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $loanModel->exchangeArrayFromForm($this->form->getData());
                $loanModel->modifiedDate = Helper::getcurrentExpressionDate();
                $loanModel->modifiedBy = $this->employeeId;
                $this->repository->edit($loanModel, $id);
                $this->flashmessenger()->addMessage("Loan Successfully Updated!!!");
                return $this->redirect()->toRoute("loan");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, ['form' => $this->form, 'id' => $id]
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('loan');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Loan Successfully Deleted!!!");
        return $this->redirect()->toRoute('loan');
    }

}