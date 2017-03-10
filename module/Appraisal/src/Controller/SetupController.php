<?php
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Appraisal\Model\Setup;
use Appraisal\Form\SetupForm;
use Appraisal\Repository\SetupRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Authentication\AuthenticationService;

class SetupController extends AbstractActionController{
    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->repository = new SetupRepository($adapter);
        $this->adapter = $adapter;
        $authService = new AuthenticationService();
        $employeeId = $authService->getStorage()->read()['employee_id'];
    }
    public function initializeForm(){
        $form = new SetupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }
    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['list'=>'hellow']);
    }
    public function addAction(){
        
    }
    public function editAction(){
        
    }
    public function deleteAction(){
        
    }
}