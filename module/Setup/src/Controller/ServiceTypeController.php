<?php

namespace Setup\Controller;

use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Model\ServiceType;
use Zend\View\View;
use Setup\Model\ServiceTypeRepository;

class ServiceTypeController extends AbstractActionController{
	private $form;
	private $serviceType;
	private $repository;

	function __construct(AdapterInterface $adapter)
	{
		$this->repository = new ServiceTypeRepository($adapter);
	}

	private function initializeForm(){
		$this->serviceType = new ServiceType();
		$builder = new AnnotationBuilder();
		if (!$this->form) {
			$this->form = $builder->createForm($this->serviceType);
		}
	}

	public function indexAction(){
		$serviceTypeList= $this->repository->fetchAll();
		$request = $this->getRequest();
		return Helper::addFlashMessagesToArray($this,['serviceTypeList' => $serviceTypeList]);
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
        	try {
	        	$this->serviceType->exchangeArrayFromForm($this->form->getData());
	        	
	        	print_r($this->form->getData());
	      
	            $this->repository->add($this->serviceType);
	            
	            $this->flashmessenger()->addMessage("Service Type Successfully Added!!!");
	            return $this->redirect()->toRoute("serviceType");
	        }
	        catch(Exception $e) {

	        }

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
        	$r = $this->repository->fetchById($id);
        	$this->serviceType->exchangeArrayFromDb($r->getArrayCopy()); // ARRAY FROM ARRAY OBJECT
        	$ab = (object)$this->serviceType->getArrayCopyForForm(); //OBJECT
				
            $this->form->bind($ab);
            return Helper::addFlashMessagesToArray($this,['form'=>$this->form,'id'=>$id]);
        }
        $modifiedDt = date("Y-m-d");

        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
            $this->serviceType->exchangeArrayFromForm($this->form->getData());

            $this->repository->edit($this->serviceType,$id,$modifiedDt);

            $this->flashmessenger()->addMessage("Service Type Successfully Updated!!!");
            return $this->redirect()->toRoute("serviceType");
        } else {
            return Helper::addFlashMessagesToArray($this,['form'=>$this->form,'id'=>$id]);

        }
	}
	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
		$this->repository->delete($id);
		$this->flashmessenger()->addMessage("Service Type Successfully Deleted!!!");
		return $this->redirect()->toRoute('serviceType');
	}
}
	


?>