<?php

namespace Asset\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Asset\Form\IssueForm;
use Asset\Repository\IssueRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;


class IssueController extends AbstractActionController
{
    private $adapter;
    private $form;
    private $repository;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new IssueRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
        
    }
    public function initializeForm() {
        $form = new IssueForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }
    
    public function indexAction() {
//        echo 'prabin';
//        die();
    }
    
    public function addAction(){
       $asset=$this->repository->fetchallIssuableAsset();
       $assetFormElement = new Select();
       $assetFormElement->setName("asset");
       $assetFormElement->setValueOptions($asset['B']);
       $assetFormElement->setAttributes(["id" => "assetId", "class" => "form-control"]);
       $assetFormElement->setLabel("Asset");
//       $asset=EntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::ASSET_ID, [Setup::ASSET_EDESC], ["STATUS" => "E"], Setup::ASSET_EDESC, "ASC",NULL,FALSE,TRUE);
//       echo'<pre>';
//       print_r($asset['B']);
//       die();
       
               
      return Helper::addFlashMessagesToArray($this, [
                    'assetForm' => $assetFormElement,
                    'asset' => $asset['A'],
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }
    
}

