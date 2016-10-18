<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 1:25 PM
 */
namespace System\Controller;

use System\Form\RoleSetupForm;
use System\Model\RoleSetup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Helper\Helper;
use System\Repository\RoleSetupRepository;

class RoleSetupController extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new RoleSetupRepository($adapter);
        $this->adapter = $adapter;
    }

    public function initializeForm(){
        $roleSetupForm = new RoleSetupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($roleSetupForm);
    }

    public function indexAction()
    {
        $list = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['list' => $list]);
    }
    public function addAction(){
        $request = $this->getRequest();
        $this->initializeForm();

        if($request->isPost()){
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $roleSetup = new RoleSetup();
                $roleSetup->exchangeArrayFromForm($this->form->getData());
                $roleSetup->roleId = ((int)Helper::getMaxId($this->adapter, RoleSetup::TABLE_NAME, RoleSetup::ROLE_ID)) + 1;
                $roleSetup->createdDt = Helper::getcurrentExpressionDate();
                $roleSetup->status='E';

                $this->repository->add($roleSetup);

                $this->flashmessenger()->addMessage("Role Successfully Added!!!");
                return $this->redirect()->toRoute("rolesetup");
            }
        }
        return Helper::addFlashMessagesToArray($this,['form'=>$this->form]);
    }
    public function editAction(){
        $id = (int)$this->params()->fromRoute("id");
        $this->initializeForm();
        $request = $this->getRequest();

        $roleSetup = new RoleSetup();
        if (!$request->isPost()) {
            $roleSetup->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($roleSetup);
        } else {
            $modifiedDt = date('d-M-y');
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $roleSetup->exchangeArrayFromForm($this->form->getData());
                $roleSetup->modifiedDt = Helper::getcurrentExpressionDate();
                unset($roleSetup->createdDt);
                unset($roleSetup->roleId);
                unset($roleSetup->status);
                $this->repository->edit($roleSetup, $id);
                $this->flashmessenger()->addMessage("Role Successfully Updated!!!");
                return $this->redirect()->toRoute("rolesetup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id
        ]);
    }
    public function deleteAction(){
        $id = (int)$this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('rolesetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Role Successfully Deleted!!!");
        return $this->redirect()->toRoute('rolesetup');
    }
}