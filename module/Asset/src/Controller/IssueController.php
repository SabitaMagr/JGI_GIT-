<?php

namespace Asset\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Asset\Form\IssueForm;
use Asset\Model\Setup;
use Asset\Repository\IssueRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
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
//       $asset=EntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::ASSET_ID, [Setup::ASSET_EDESC], ["STATUS" => "E"], Setup::ASSET_EDESC, "ASC",NULL,FALSE,TRUE);
//       echo'<pre>';
//       print_r($asset);
//       die();
       
               
      return Helper::addFlashMessagesToArray($this, [
                    'asset' => 'asset',
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }
    
}

