<?php
namespace Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;
use Zend\Form\Annotation\AnnotationBuilder;
use Setup\Form\LoanAdvanceForm;

class LoanAdvanceController extends AbstractActionController{
    private $form;
    
    public function _construct(){
        
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new LoanAdvanceForm();
        $this->form = $builder->createForm($form);
    }
    
    public function indexAction(){
        return Helper::addFlashMessagesToArray($this, ['list'=>'list']);       
    } 
    
    public function addAction(){
        $this->initializeForm();
        $request = $this->getRequest();
        $loanTypeElement = array(
            "1"=>"Loan",
            "2"=>"Advance"
        );
        
        if($request->isPost()){
            return Helper::addFlashMessagesToArray($this, ['list'=>'list']);       
        }else{
            return Helper::addFlashMessagesToArray($this, ['form'=>$this->form,"loanTypeElement"=>$loanTypeElement]);
        }        
    }
}