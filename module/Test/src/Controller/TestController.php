<?php
namespace Test\Controller;

use Zend\Mvc\Test;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Test\Form\EmployeeForm;
use Zend\Form\Annotation\AnnotationBuilder;



class TestController extends AbstractActionController
{
    protected $form;

    public function getForm(){
      
        if(!$this->form){
            $employeeForm = new EmployeeForm();
            $builder = new AnnotationBuilder();
            $this->form=$builder->createForm($employeeForm);
        }
        return $this->form;
    }

    public function indexAction()
    {
       
    }

    public function addAction()
    {
        $form = $this->getForm();

         return new ViewModel([
            'form' => $form,
            'messages' => $this->flashmessenger()->getMessages()
        ]);
    }

    public function editAction()
    {
    }

    public function deleteAction()
    {
    }
}