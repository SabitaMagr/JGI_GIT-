<?php

namespace Asset\Controller;

use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Asset\Form\IssueForm;
use Asset\Model\Setup;
use Setup\Model\HrEmployees;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;


class IssueController extends AbstractActionController
{
    private $adapter;
    private $form;
    
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        
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
        $this->initializeForm();
        
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'asset' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::ASSET_ID, [Setup::ASSET_EDESC], ["STATUS" => "E"], Setup::ASSET_EDESC, "ASC"),
                    'employee' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " "),
        ]);
        
    }
    
}

