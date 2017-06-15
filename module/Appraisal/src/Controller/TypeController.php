<?php
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;
use Appraisal\Model\Type;
use Appraisal\Repository\TypeRepository;
use Appraisal\Form\TypeForm;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\AuthenticationService;
use Setup\Model\ServiceType;
use Application\Helper\EntityHelper;
use Setup\Repository\EmployeeRepository;

class TypeController extends AbstractActionController{
    private $repository;
    private $adapter;
    private $form;
    private $userId;
    private $employeeId;
            
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository =  new TypeRepository($adapter);
        
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
        $this->userId = $authService->getStorage()->read()['user_id'];
    }
    
    public function initializeForm(){
        $typeForm = new TypeForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($typeForm);
        }
    }
    
    public function indexAction() {
        $list = $this->repository->fetchAll();
        $result = [];
        foreach($list as $data){
            array_push($result, $data);
        }
        return Helper::addFlashMessagesToArray($this, ['list'=>$result]);
    }
    
    public function addAction(){
        $this->initializeForm();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        
        $request = $this->getRequest();
        if($request->isPost()){
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $type = new Type();
                $type->exchangeArrayFromForm($this->form->getData());
                $type->createdDate = Helper::getcurrentExpressionDate();
                $type->approvedDate = Helper::getcurrentExpressionDate();
                $type->createdBy = $this->employeeId;
                $type->companyId = $employeeDetail['COMPANY_ID'];
                $type->branchId = $employeeDetail['BRANCH_ID'];
                $type->appraisalTypeId = ((int) Helper::getMaxId($this->adapter, "HRIS_APPRAISAL_TYPE", "APPRAISAL_TYPE_ID")) + 1;
                $type->status = 'E';
                $this->repository->add($type);
                $this->flashmessenger()->addMessage("Appraisal Type Successfully added!!!");
                return $this->redirect()->toRoute("type");
            }
        }
        return Helper::addFlashMessagesToArray($this, 
                [
                    'form'=>$this->form
                ]);
        
    }
    public function editAction(){
        $id = $this->params()->fromRoute('id');
        if($id==0){
            $this->redirect()->toRoute('type');
        }
        $this->initializeForm();
        
        $request = $this->getRequest();
        $type= new Type();
        if(!$request->isPost()){
            $type->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($type);
        }else{
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
                $type->exchangeArrayFromForm($this->form->getData());
                $type->modifiedDate = Helper::getcurrentExpressionDate();
                $type->modifiedBy = $this->employeeId;
                $this->repository->edit($type, $id);
                $this->flashmessenger()->addMessage("Appraisal Type Successfully Updated!!!");
                return $this->redirect()->toRoute("type");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form'=>$this->form,
            'id'=>$id
        ]);
    }
    public function deleteAction(){
        $id = $this->params()->fromRoute('id');
        if($id==0){
            $this->redirect()->toRoute('type');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Appraisal Type Successfully Deleted!!!");
        return $this->redirect()->toRoute("type");
    }
}