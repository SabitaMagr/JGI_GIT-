<?php
namespace Setup\Controller;

use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Setup\Model\Company;

class CompanyController extends AbstractActionController{

	protected $form;

	public function getForm(){
		$company = new Company();
		$builder = new AnnotationBuilder();
		if (!$this->form) {
			$this->form = $builder->createForm($company);
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