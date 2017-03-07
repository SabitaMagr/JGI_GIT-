<?php

namespace Asset\Controller;

use Application\Helper\Helper;
use Asset\Form\GroupForm;
use Asset\Model\Group;
use Asset\Repository\GroupRepository;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class GroupController extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new GroupRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $form = new GroupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        return [
            'a' => 'sdf'
        ];
    }

    public function addAction() {
        $this->initializeForm();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        
//        echo '<pre>';
//        print_r($employeeDetail);
//        echo '</pre>';
//        die();
        
        $request = $this->getRequest();
        if($request->isPost()){
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                
                $group = new Group();
                $group->exchangeArrayFromForm($this->form->getData());
                $group->createdBy=$this->employeeId;
                $group->createdDate=Helper::getcurrentExpressionDate();
                $group->approveDate= Helper::getcurrentExpressionDate();
                $group->companyId=$employeeDetail['COMPANY_ID'];
                $group->branchId=$employeeDetail['BRANCH_ID'];
                $group->assetGroupId= ((int) Helper::getMaxId($this->adapter, $group::TABLE_NAME, $group::ASSET_GROUP_ID)) + 1;
                
                $group->status= 'E';
                $this->repository->add($group);
                $this->flashmessenger()->addMessage("Asset Group Successfully added!!!");
                
//                print_r($this->flashmessenger()->getMessage());
//die();
                return $this->redirect()->toRoute("assetGroup");
                
                echo '<pre>';
                print_r($group);
                echo '</pre>';
//                echo 'valid';
   
           
                die();
            }
              echo '  not valid';
              die();
            
        }

        return[
            'form' => $this->form,
        ];
    }
    

}
