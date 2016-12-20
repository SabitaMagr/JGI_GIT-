<?php
namespace Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;
use Zend\Form\Annotation\AnnotationBuilder;
use Setup\Form\TrainingForm;

class TrainingController extends AbstractActionController{
    private $form;
    public function _construct(){
        
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new TrainingForm();
        $this->form = $builder->createForm($form);
    }
    
    public function indexAction(){
        return Helper::addFlashMessagesToArray($this, ['list'=>'list']);       
    } 
    
    public function addAction(){
        $this->initializeForm();
        $request = $this->getRequest();
        
        if($request->isPost()){
            return Helper::addFlashMessagesToArray($this, ['list'=>'list']);       
        }else{
            return Helper::addFlashMessagesToArray($this, ['form'=>$this->form]);
        }        
    }
}