<?php
namespace Setup\Controller;

use Application\Helper\Helper;
use Setup\Form\InstituteForm;
use Setup\Model\Institute;
use Setup\Repository\InstituteRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class InstituteController extends AbstractActionController{
    private $form;
    private $adapter;
    private $repository;
    private $employeeId;
        
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $this->repository = new InstituteRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new InstituteForm();
        $this->form = $builder->createForm($form);
    }
    
    public function indexAction(){
        $list = $this->repository->fetchActiveRecord();
        return Helper::addFlashMessagesToArray($this, ['list'=>$list]);       
    } 
    
    public function addAction(){
        $this->initializeForm();
        $request = $this->getRequest();       
                    
        if($request->isPost()){
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
               $instituteModel = new Institute();   
               $instituteModel->exchangeArrayFromForm($this->form->getData());
               $instituteModel->instituteId = ((int) Helper::getMaxId($this->adapter, Institute::TABLE_NAME, Institute::INSTITUTE_ID))+1;
               $instituteModel->createdDate = Helper::getcurrentExpressionDate();
               $instituteModel->status = 'E';
               $instituteModel->createdBy = $this->employeeId;               
               $this->repository->add($instituteModel);
               $this->flashmessenger()->addMessage("Institute Successfully added!!!");
               return $this->redirect()->toRoute('institute');
            }       
        }
        return Helper::addFlashMessagesToArray($this, ['form'=>$this->form]);              
    }
    
    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('institute');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $instituteModel = new Institute();
        if (!$request->isPost()) {
            $instituteModel->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($instituteModel);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $instituteModel->exchangeArrayFromForm($this->form->getData());
                $instituteModel->modifiedDate = Helper::getcurrentExpressionDate();
                $instituteModel->modifiedBy = $this->employeeId;
                $this->repository->edit($instituteModel, $id);
                $this->flashmessenger()->addMessage("Institute Successfully Updated!!!");
                return $this->redirect()->toRoute("institute");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, ['form' => $this->form, 'id' => $id]
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('institute');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Institute Successfully Deleted!!!");
        return $this->redirect()->toRoute('institute');
    }

}