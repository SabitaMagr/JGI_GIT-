<?php

namespace Setup\Controller;

use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Model\Position;
use Setup\Model\PositionRepositoryInterface;

class PositionController extends AbstractActionController{
	
	private $repository;

	public function __construct(PositionRepositoryInterface $repository){
		$this->repository = $repository;
	}

	

	public function indexAction(){
		$position = $this->repository->fetchAll();
		$request  = $this->getRequest();

		return new viewModel([
			'positions' => $position
			]);

		
	}
	public function addAction(){
		$position = new Position();
		$builder = new AnnotationBuilder();
		$form = $builder->createForm($position);
		

		$request = $this->getRequest();

		if(!$request->isPost()){
	        return new ViewModel([
	            'form' => $form,
	            'messages' => $this->flashmessenger()->getMessages()
	        ]);
	    }

	    $form->setData($request->getPost());

	    if($form->isValid()){
	    	$position->exchangeArray($form->getData());
	    	$this->repository->addPosition($position);
	    	return $this->redirect()->toRoute('position');
	    }else{
	    	return ['form'=>$form]; 
        }
	}


	public function editAction(){

	}
	public function deleteAction(){

	}
}
	


?>