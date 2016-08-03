<?php

namespace Setup\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Model\Position;

class PositionController extends AbstractActionController{
	private $adapter;

	function __construct(AdapterInterface $adapter)
	{
		$this->adapter=$adapter;
	}


	protected $form;

	public function getForm(){
		$position = new Position();
		$builder = new AnnotationBuilder();
		if (!$this->form) {
			$this->form = $builder->createForm($position);
		}
		return $this->form;
	}

	public function indexAction(){

	}
	public function addAction(){
		$form = $this->getForm();

        return new ViewModel([
            'form' => $form,
            'messages' => $this->flashmessenger()->getMessages()
        ]);
	}
	public function editAction(){

	}
	public function deleteAction(){

	}
}
	


?>