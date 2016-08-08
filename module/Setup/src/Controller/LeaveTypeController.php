<?php
namespace Setup\Controller;

use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\ViewModel;
use Zend\View\View;
use Setup\Model\LeaveType;
use Setup\Model\LeaveTypeRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;


class LeaveTypeController extends AbstractActionController {

	private $repository;
	private $form;
	private $leaveType;

	public function __construct(AdapterInterface $adapter){
		$this->repository =  new LeaveTypeRepository($adapter);
	}

	public function initializeForm(){
		$this->leaveType = new LeaveType();
		$builder =  new AnnotationBuilder();
		$this->form = $builder -> createForm($this->leaveType);
	}

	public function indexAction(){
		$leaveTypeList = $this->repository->fetchAll();

		return Helper::addFlashMessagesToArray($this,['leaveTypeList'=>$leaveTypeList]);
	}

	public function addAction(){
		$this->initializeForm();

		$request = $this->getRequest();

		if(!$request->isPost()){
			return Helper::addFlashMessagesToArray($this,['form'=>$this->form]);
		}

		$this->form->setData($request->getPost());

		if($this->form->isValid()){
			$this->leaveType->exchangeArray($this->form->getData());
			$this->repository->add($this->leaveType);
			$this->flashmessenger()->addMessage("Leave Type Successfully Added!!!");
			return $this->redirect()->toRoute("leaveType");
		}else{
			return Helper::addFlashMessagesToArray($this,['form'=>$this->form]);
		}
	}

	public function editAction()
	{
	   
	    $id=(int) $this->params()->fromRoute("id");
        if($id===0){
            return $this->redirect()->toRoute('leaveType');
        }
        $this->initializeForm();

        $request=$this->getRequest();

        if(!$request->isPost()){
            $this->form->bind($this->repository->fetchById($id));
            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->form,'id'=>$id]
                );
        }

        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
            $this->leaveType->exchangeArray($this->form->getData());
            $this->repository->edit($this->leaveType,$id);
            $this->flashmessenger()->addMessage("Leave Type Successfully Updated!!!");
           return $this->redirect()->toRoute("leaveType");
        } else {
            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->form,'id'=>$id]
             );

        }
          
	}

	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Leave Type Successfully Deleted!!!");
        return $this->redirect()->toRoute('leaveType');
	}

}