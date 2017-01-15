<?php
namespace SelfService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\EntityHelper;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\HrEmployees;
use Training\Repository\TrainingAssignRepository;
use Zend\Authentication\AuthenticationService;

class TrainingList extends AbstractActionController{
    private $form;
    private $adapter;
    private $trainingAssignRepo;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->trainingAssignRepo = new TrainingAssignRepository($this->adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }
    public function indexAction() {
        $result = $this->trainingAssignRepo->getAllTrainingList($this->employeeId);
        $list = [];
        $getValue = function($trainingTypeId){
            if($trainingTypeId=='CC'){
                return 'Company Contribution';
            }else if($trainingTypeId=='CP'){
                return 'Company Personal';
            }
        };
        foreach($result as $row){
            $row['TRAINING_TYPE']= $getValue($row['TRAINING_TYPE']);
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, ['list'=>$list]);
    }
    public function viewAction(){
        $employeeId = (int) $this->params()->fromRoute("employeeId");
        $trainingId = (int) $this->params()->fromRoute("trainingId");
        
        if(!$employeeId && !$trainingId){
            return $this->redirect()->toRoute('trainingList');
        }
        
        $detail = $this->trainingAssignRepo->getDetailByEmployeeID($employeeId, $trainingId);
        
        ///print_r($detail); die();
        
        return Helper::addFlashMessagesToArray($this,['detail'=>$detail]);
    }
}        