<?php

namespace SelfService\Controller;

use Application\Helper\Helper;
use SelfService\Model\BirthdayModel;
use SelfService\Repository\BirthdayRepository;
use Setup\Repository\EmployeeRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;


class Birthday extends AbstractActionController {

    private $adapter;
    private $repository;
    private $employeeId;

// constructor
    function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
       $this->repository = new BirthdayRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $birthdays = $this->repository->getBirthdays();
        
//        $testArr = array(                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
//        1000370 => array(
//        'EMPLOYEE_ID' => 1000370,
//        'FULL_NAME' => 'Somkala Pachhai',
//        'DESIGNATION_TITLE' => 'Php Developer',
//        'FILE_PATH' => '1499430267.jpg',
//        'BIRTH_DATE' => '12-NOV-94',
//        'EMP_BIRTH_DATE' => '12th November',
//        'BIRTHDAYFOR' => 'TODAY'
//        ),
//             102 => array(
//        'EMPLOYEE_ID' => 102,
//        'FULL_NAME' => 'Abbey  Mathew',
//        'DESIGNATION_TITLE' => 'DotNet Developer',
//        'FILE_PATH' => '',
//        'BIRTH_DATE' => '27-NOV-86',
//        'EMP_BIRTH_DATE' => '27th November',
//        'BIRTHDAYFOR' => 'TODAY'
//        )
//
//        );
//
//        $birthdays['TODAY'] = $testArr;
//

       
//        $birthdays = ['test' => 'Happy Birthday'];
        return Helper::addFlashMessagesToArray($this, [
                    'employeesBirthday' => $birthdays,
                    'currentEmployeeId' => $this->employeeId
        ]); //return the values
    }
    
    





    public function wishAction(){
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('birthday');
        }
        
         $empRepo= new EmployeeRepository($this->adapter);
        $empDetails=$empRepo->fetchById($id);
        $request = $this->getRequest();
        
        $birthdayMessage=$this->repository->getBirthdayMessage($id);
        
        if($request->isPost()){
            $birthdayModel= new BirthdayModel();
            $data=$request->getPost();
            $message=$data['birthdayMessage'];
            
          $birthdayModel->birthdayId=((int) Helper::getMaxId($this->adapter, BirthdayModel::TABLE_NAME, BirthdayModel::BIRTHDAY_ID)) + 1;
          $birthdayModel->birthdayDate=$empDetails['BIRTH_DATE'];
          $birthdayModel->fromEmployee=$this->employeeId;
          $birthdayModel->toEmployee=$id;
          $birthdayModel->message=$message;
          $birthdayModel->createdDt=Helper::getcurrentExpressionDate();
          $birthdayModel->status='E';
          
          $this->repository->add($birthdayModel);
          $this->flashmessenger()->addMessage("Birthday message created sucessfully");
          return $this->redirect()->toRoute("birthday");
               
        }
        
        $messagePosted=$this->repository->checkMessagePosted($this->employeeId,$id);
//        print_r($messagePosted['C']);
//        die();
                
        
        
        
        $showMessageField=true;
        if($empDetails['EMPLOYEE_ID']==$this->employeeId){
        $showMessageField=false;
        }
        if($messagePosted['C']>0){
        $showMessageField=false;
        }
        
       
        
   
        

        
        return Helper::addFlashMessagesToArray($this, [
                    'brithdayEmpDtl' => $empDetails,
                    'birthdayMessage'=>$birthdayMessage,
                    'showMessageField'=>$showMessageField,
                    'checkMessagePosted'=>$checkMessagePosted,
                    
                              
        ]);
    }

}
