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
use Zend\View\Model\ViewModel;
use Setup\Form\LeaveTypeForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\LeaveTypeRepository;


class LeaveTypeController extends AbstractActionController {

	private $leaveType;
	private $form;
	private $repository;

	public function __construct(AdapterInterface $adapter){
		$this->repository = new LeaveTypeRepository($adapter);
	}

	public function initializeForm(){

		$this->leaveType = new LeaveTypeForm();
		$builder =  new AnnotationBuilder();	
		$this->form = $builder -> createForm($this->leaveType);
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
				$this->leaveType->exchangeArrayFromForm($this->form->getData());
				$this->repository->add($this->leaveType);
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
        $modifiedDt = date("d-M-y");
        
        if(!$request->isPost()){
        	$this->leaveType->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
        	$this->form->bind((object)$this->leaveType->getArrayCopyForForm());           
        }else{

	        $this->form->setData($request->getPost());
	        if ($this->form->isValid()) {

	        	$this->leaveType->exchangeArrayFromForm($this->form->getData());
	        	$this->repository->edit($this->leaveType,$id,$modifiedDt);
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
        $this->hrLeaveTypes =  $this->entityManager->find(HrLeaveTypes::class, $id);
        $this->entityManager->remove($this->hrLeaveTypes);
        $this->entityManager->flush();

        $this->flashmessenger()->addMessage("Leave Type Successfully Deleted!!!");
        return $this->redirect()->toRoute('leaveType');
	}

}

/* End of file LeaveTypeController.php */
/* Location: ./Setup/src/Controller/LeaveTypeController.php */