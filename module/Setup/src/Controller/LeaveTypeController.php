<?php
namespace Setup\Controller;

/**
* Master Setup for Leave Type
* Leave Type controller.
* Created By: Somkala Pachhai
* Edited By: Somkala Pachhai
* Date: August 3, 2016, Wednesday 
* Last Modified By: Somkala Pachhai
* Last Modified Date: August 22,2016, Monday 
*/

use Application\Helper\Helper;
use Setup\Model\LeaveType;
use Zend\View\Model\ViewModel;
use Setup\Form\LeaveTypeForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Repository\LeaveTypeRepository;


class LeaveTypeController extends AbstractActionController {

	private $form;
	private $repository;

	public function __construct(AdapterInterface $adapter){
		$this->repository = new LeaveTypeRepository($adapter);
	}

	public function initializeForm(){

		$leaveTypeForm = new LeaveTypeForm();
		$builder =  new AnnotationBuilder();	
		$this->form = $builder -> createForm($leaveTypeForm);
	}

	public function indexAction(){
		$leaveTypeList = $this->repository->fetchAll();
		return Helper::addFlashMessagesToArray($this,['leaveTypeList'=> $leaveTypeList]);
	}

	public function addAction(){
		$this->initializeForm();
		$request = $this->getRequest();
		if($request->isPost()){
				
			$this->form->setData($request->getPost());
			if($this->form->isValid()){
				$leaveType=new LeaveType();
				$leaveType->exchangeArrayFromForm($this->form->getData());
				$leaveType->createdDt=date('d-M-y');
				$this->repository->add($leaveType);
				$this->flashmessenger()->addMessage("Leave Type Successfully Added!!!");
				return $this->redirect()->toRoute("leaveType");

			}
		}
		return Helper::addFlashMessagesToArray($this,['form'=>$this->form]);	
	}

	public function editAction()
	{
	   
	    $id=(int) $this->params()->fromRoute("id");
        if($id===0){
            return $this->redirect()->toRoute('leaveType');
        }
        $this->initializeForm();
        $request=$this->getRequest();

		$leaveType=new LeaveType();
        if(!$request->isPost()){
        	$leaveType->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
        	$this->form->bind($leaveType);
        }else{

	        $this->form->setData($request->getPost());
	        if ($this->form->isValid()) {

	        	$leaveType->exchangeArrayFromForm($this->form->getData());
				$leaveType->modifiedDt=date('d-M-y');
	        	$this->repository->edit($leaveType,$id);
	            $this->flashmessenger()->addMessage("Leave Type Successfully Updated!!!");
	            return $this->redirect()->toRoute("leaveType");
	        }
    	}
        return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->form,'id'=>$id]
                );
          
	}

	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Leave Type Successfully Deleted!!!");
        return $this->redirect()->toRoute('leaveType');
	}

}

/* End of file LeaveTypeController.php */
/* Location: ./Setup/src/Controller/LeaveTypeController.php */