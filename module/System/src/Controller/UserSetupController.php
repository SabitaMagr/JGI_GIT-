<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 1:25 PM
 */
namespace System\Controller;

use Application\Helper\EntityHelper;
use System\Form\UserSetupForm;
use System\Model\UserSetup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Helper\Helper;
use System\Repository\UserSetupRepository;
use Zend\Authentication\AuthenticationService;

class UserSetupController extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;
    private $employeeId;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new UserSetupRepository($adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm(){
        $roleSetupForm = new UserSetupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($roleSetupForm);
    }

    public function indexAction()
    {
        $list = $this->repository->fetchAll();
        $users = [];
        foreach($list as $row){
            array_push($users, $row);
        }
        return Helper::addFlashMessagesToArray($this, ['users' => $users]);
    }
    public function addAction(){
        $request = $this->getRequest();
        $this->initializeForm();

        if($request->isPost()){
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $userSetup = new UserSetup();
                $userSetup->exchangeArrayFromForm($this->form->getData());
                $userSetup->userId = ((int)Helper::getMaxId($this->adapter, UserSetup::TABLE_NAME, UserSetup::USER_ID)) + 1;
                $userSetup->createdDt = Helper::getcurrentExpressionDate();
                $userSetup->createdBy = $this->employeeId;
                $userSetup->status='E';

                $this->repository->add($userSetup);

                $this->flashmessenger()->addMessage("User Successfully Added!!!");
                return $this->redirect()->toRoute("usersetup");
            }
        }
        return Helper::addFlashMessagesToArray($this,[
            'form'=>$this->form,
            'employeeList'=>$this->repository->getEmployeeList(),
            'roleList'=>EntityHelper::getTableKVList($this->adapter,"HR_ROLES","ROLE_ID",["ROLE_NAME"],["STATUS"=>"E"])
        ]);
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute("id");
        $this->initializeForm();
        $request = $this->getRequest();

        $userSetup = new UserSetup();
        $detail = $this->repository->fetchById($id)->getArrayCopy();
        //print_r($detail['PASSWORD']); die();
        if (!$request->isPost()) {
            $userSetup->exchangeArrayFromDB($detail);
            $this->form->bind($userSetup);
        } else {
            $modifiedDt = date('d-M-y');
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $userSetup->exchangeArrayFromForm($this->form->getData());
                $userSetup->modifiedDt = Helper::getcurrentExpressionDate();
                $userSetup->modifiedBy = $this->employeeId;
                unset($userSetup->createdDt);
                unset($userSetup->userId);
                unset($userSetup->status);
                $this->repository->edit($userSetup, $id);
                $this->flashmessenger()->addMessage("User Successfully Updated!!!");
                return $this->redirect()->toRoute("usersetup");
            }
        }
        return Helper::addFlashMessagesToArray($this,[
            'form'=>$this->form,
            'id'=>$id,
            'passwordDtl'=>$detail['PASSWORD'],
            'employeeList'=>$this->repository->getEmployeeList($detail['EMPLOYEE_ID']),
            'roleList'=>EntityHelper::getTableKVList($this->adapter,"HR_ROLES","ROLE_ID",["ROLE_NAME"],["STATUS"=>"E"])
        ]);
    }

    public function deleteAction(){
        $id = (int)$this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('usersetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("User Successfully Deleted!!!");
        return $this->redirect()->toRoute('usersetup');
    }
}