<?php

namespace Setup\Controller;

use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Model\EmployeeType;
use Zend\View\View;
use Setup\Model\EmployeeTypeRepository;

class EmployeeTypeController extends AbstractActionController{
	private $form;
	private $employeeType;
	private $repository;

	function __construct(AdapterInterface $adapter)
	{
		$this->repository = new EmployeeTypeRepository($adapter);
	}

	public function initializeForm(){
		$this->employeeType = new EmployeeType();
		$builder = new AnnotationBuilder();
		if (!$this->form) {
			$this->form = $builder->createForm($this->employeeType);
		}
	}

	public function indexAction(){
		$employeeTypeList= $this->repository->fetchAll();
		$request = $this->getRequest();
		return Helper::addFlashMessagesToArray($this,['employeeTypeList' => $employeeTypeList]);
	}

	public function addAction(){
		
		$this->initializeForm();

        $request = $this->getRequest();
        if (!$request->isPost()) {
        	return Helper::addFlashMessagesToArray($this,[
	            'form' => $this->form,
	            'messages' => $this->flashmessenger()->getMessages()
        	]);
        }
        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {

            $this->employeeType->exchangeArray($this->form->getData());
            $this->repository->add($this->employeeType);
            $this->flashmessenger()->addMessage("Employee Type Successfully Added!!!");
            return $this->redirect()->toRoute("employeeType");
        } else {
            return Helper::addFlashMessagesToArray($this,[
	            'form' => $this->form,
	            'messages' => $this->flashmessenger()->getMessages()
        	]);
        }   
	}

	
	public function editAction(){

		$id=(int) $this->params()->fromRoute("id");
		if($id===0){
			return $this->redirect()->toRoute();
		}
        $this->initializeForm();

        $request=$this->getRequest();

        if(!$request->isPost()){
            $this->form->bind($this->repository->fetchById($id));
            return Helper::addFlashMessagesToArray($this,['form'=>$this->form,'id'=>$id]);
        }

        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
            $this->employeeType->exchangeArray($this->form->getData());
            $this->repository->edit($this->employeeType,$id);
            $this->flashmessenger()->addMessage("Employee Type Successfully Updated!!!");
           return $this->redirect()->toRoute("employeeType");
        } else {
            return Helper::addFlashMessagesToArray($this,['form'=>$this->form,'id'=>$id]);

        }
	}
	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
		$this->repository->delete($id);
		$this->flashmessenger()->addMessage("Employee Type Successfully Deleted!!!");
		return $this->redirect()->toRoute('employeeType');
	}
}
	


?>