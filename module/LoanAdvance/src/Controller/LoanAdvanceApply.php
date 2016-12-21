<?php
namespace LoanAdvance\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Form\LoanAdvanceRequestForm;
use Setup\Model\HrEmployees;

class LoanAdvanceApply extends AbstractActionController{
    private $form;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new LoanAdvanceRequestForm();
        $this->form = $builder->createForm($form);
    }
    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['list'=>'list']);
    }
    public function addAction(){
        $this->initializeForm();
       $employee = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME , HrEmployees::EMPLOYEE_ID, [ HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS=>'E'], " ");
       $loanAdvanceList = array(
           '1' =>'Building Loan',
           '2'=>'Salary Advance'
       );
       return Helper::addFlashMessagesToArray($this, [
           'form'=>$this->form,
           'employees'=>$employee,
           'loanAdvanceList'=>$loanAdvanceList
           ]);
    }
}