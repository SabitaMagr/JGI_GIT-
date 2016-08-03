<?php

namespace Setup\Controller;

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
		return new ViewModel([
			'employeeTypeList'=>$employeeTypeList
			]);
	}

	public function addAction(){
		$this->initializeForm();

		$request = $this->getRequest();
		if(!$request->isPost()){
			return ['form'=>$this->form];
		}

		
	}

	
	public function editAction(){

	}
	public function deleteAction(){

	}
}
	


?>