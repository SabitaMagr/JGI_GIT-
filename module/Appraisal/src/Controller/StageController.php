<?php
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Appraisal\Repository\StageRepository;
use Appraisal\Form\StageForm;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Authentication\AuthenticationService;
use Application\Helper\Helper;

class StageController extends AbstractActionController{
    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    private $userId;
    
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $this->repository = new StageRepository($adapter);
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
        $this->userId = $authService->getStorage()->read()['user_id'];
    }
    
    public function initializeForm(){
        $form = new StageForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }
    
    public function indexAction() {
        $result = $this->repository->fetchAll();
        $list = [];
        foreach($result as $row){
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
            "stages"=>$list
        ]);
    }
    
    public function addAction(){
        $this->initializeForm();
        $request = $this->getRequest();
        if($request->isPost()){
            
        }else{
            return Helper::addFlashMessagesToArray($this, [
                'form'=>$this->form
            ]);
        }
    }
    public function editAction(){
        
    }
    public function deleteAction(){
        
    }
}