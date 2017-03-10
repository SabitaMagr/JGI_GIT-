<?php

namespace Asset\Controller;

use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Asset\Form\SetupForm;
use Asset\Model\Group;
use Asset\Model\Setup;
use Asset\Repository\SetupRepository;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class SetupController extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new SetupRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $form = new SetupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $result = $this->repository->fetchAll();
        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'setup' => $list
        ]);
    }

    public function addAction() {
        $this->initializeForm();
        $employeeRepo = new EmployeeRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $setup = new Setup();
                $setup->exchangeArrayFromForm($this->form->getData());
                $setup->createdBy = $this->employeeId;
                $setup->createdDate = Helper::getcurrentExpressionDate();
                $setup->approvedDate = Helper::getcurrentExpressionDate();
                $setup->companyId = $employeeDetail['COMPANY_ID'];
                $setup->branchId = $employeeDetail['BRANCH_ID'];
                $setup->assetId = ((int) Helper::getMaxId($this->adapter, $setup::TABLE_NAME, $setup::ASSET_ID)) + 1;
                $setup->status = 'E';
                $setup->purchaseDate = Helper::getExpressionDate($setup->purchaseDate);
                $setup->expiaryDate = Helper::getExpressionDate($setup->expiaryDate);
                $this->repository->add($setup);
                $this->flashmessenger()->addMessage("Asset Successfully added!!!");
                return $this->redirect()->toRoute("assetSetup");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
//            'group'=>$groupList
                    'group' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Group::TABLE_NAME, Group::ASSET_GROUP_ID, [Group::ASSET_GROUP_EDESC], ["STATUS" => "E"], Group::ASSET_GROUP_EDESC, "ASC"),
        ]);
    }

    public function editAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('assetSetup');
        }
        $this->initializeForm();
        $request = $this->getRequest();
        $setup= new Setup();
        if(!$request->isPost()){
            $setup->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($setup);
        }else{
//            $this->form->setData($request->getPost());
//            if($this->form->isValid()){
//                $setup->exchangeArrayFromForm($this->form->getData());
//                
//                $setup->modifiedDate = Helper::getcurrentExpressionDate();
//                $setup->modifiedBy = $this->employeeId;
//                
//                $this->repository->edit($group, $id);
//                $this->flashmessenger()->addMessage("Asset Group Successfully Updated!!!");
//                return $this->redirect()->toRoute("assetGroup");
//            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form'=>$this->form,
            'id'=>$id
        ]);
        
    }

}
