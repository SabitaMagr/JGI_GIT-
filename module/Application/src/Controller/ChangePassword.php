<?php

namespace Application\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use System\Repository\UserSetupRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class ChangePassword extends AbstractActionController {

    private $adapter;
    private $repository;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new UserSetupRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $userLoginData = $this->repository->getUserByEmployeeId($this->employeeId);
        $oldPassword=$userLoginData['PASSWORD'];
        return Helper::addFlashMessagesToArray($this, [
            'oldPassword'=>$oldPassword
        ]);
    }

    public function updatePasswordAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $newPassword = $data['newPassword'];
            
//            echo '<pre>';
//            print_r($data);
//            die();
//            $this->repository->updateByEmpId($this->employeeId, $newPassword);
//            $this->flashmessenger()->addMessage("successfully changed your Password");
//            return $this->redirect()->toRoute("changePwd");
        } else {
//            return $this->redirect()->toRoute("changePwd");
        }
    }
    
    
    public function getOldUserPassword(){
        $userLoginData = $this->repository->getUserByEmployeeId($this->employeeId);
        $oldPassword=$userLoginData['PASSWORD'];
        return new CustomViewModel([
            'sucess'=>true,
            'oldPassword'=>$oldPassword
        ]);
    }



}
