<?php

namespace Notification\Controller;

use Application\Helper\Helper;
use Notification\Form\NewsForm;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;


class NewsController extends AbstractActionController {
    
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    
     public function initializeForm() {
        $form = new NewsForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }
    
    public function indexAction() {
//        echo 'index Action News';
//        die();
    }
    
    
    public function addAction(){
        $this->initializeForm();
//        $employeeRepo = new EmployeeRepository($this->adapter);
//        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            
        }
        
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form
                    
                    
        ]);
    }
    
}