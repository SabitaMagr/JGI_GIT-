<?php
namespace Asset\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Asset\Form\GroupForm;
use Asset\Model\Group;
use Asset\Repository\GroupRepository;;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Authentication\AuthenticationService;
use Application\Helper\Helper;
use Setup\Repository\EmployeeRepository;

class GroupController extends AbstractActionController{
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
    
    public function addAction(){
        $this->initializeForm();
        $request = $this->getRequest();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        
        if($request->isPost()){
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
                $group = new Group();
                $group->exchangeArrayFromForm($this->form->getData());
                $group->createdDate = Helper::getcurrentExpressionDate();
                $group->approvedDate = Helper::getcurrentExpressionDate();
                $group->createdBy = $this->employeeId;
                $group->companyId = $employeeDetail['COMPANY_ID'];
                $group->branchId = $employeeDetail['BRANCH_ID'];
                $group->assetGroupId= ((int) Helper::getMaxId($this->adapter, $group::TABLE_NAME, $group::ASSET_GROUP_ID)) + 1;
                $group->status = 'E';
                $this->repository->add($group);
                $this->flashmessenger()->addMessage("Asset Group Successfully added!!!");
                return $this->redirect()->toRoute("assetGroup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form'=>$this->form
        ]);
    }
}