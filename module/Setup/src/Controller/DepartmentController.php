<?php

namespace Setup\Controller;

use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Model\Department;

class DepartmentController extends AbstractActionController{
	protected $form;

	public function getForm(){
		$department = new Department();
		$builder = new AnnotationBuilder();
		if (!$this->form) {
			$this->form = $builder->createForm($department);
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