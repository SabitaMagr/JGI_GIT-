<?php
namespace Training\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\EntityHelper;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\HrEmployees;
use Training\Form\TrainingAssignForm;

class TrainingAssignController extends AbstractActionController{
    private $form;
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['list'=>'list']);
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new TrainingAssignForm();
        $this->form = $builder->createForm($form);
    }
    public function addAction(){
       $this->initializeForm();
       $employee = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME , HrEmployees::EMPLOYEE_ID, [ HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS=>'E'], " ");
       $trainingList = array(
           '1' =>'',
           '2'=>'',
           '3'=>''
       );
       return Helper::addFlashMessagesToArray($this, [
           'form'=>$this->form,
           'employees'=>$employee,
           'training'=>$trainingList
           ]); 
    }
}        