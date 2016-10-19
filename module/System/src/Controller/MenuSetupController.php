<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/18/16
 * Time: 12:01 PM
 */
namespace System\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use System\Form\MenuSetupForm;
use System\Model\MenuSetup;
use System\Repository\MenuSetupRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\Navigation\Menu;

class MenuSetupController extends AbstractActionController {

    private $repository;
    private $adapter;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new MenuSetupRepository($adapter);
        $this->adapter = $adapter;
    }

    public function initializeForm(){
        $menuSetupForm = new MenuSetupForm();
        $builder = new AnnotationBuilder();
        $this->form =  $builder->createForm($menuSetupForm);
    }

    public function indexAction()
    {
        $list = $this->repository->fetchAll();

        $request = $this->getRequest();
        $this->initializeForm();

        $menuList = EntityHelper::getTableKVList($this->adapter,MenuSetup::TABLE_NAME,MenuSetup::MENU_ID,[MenuSetup::MENU_NAME],[MenuSetup::STATUS=>"E"]);
        $menuList[-1]="None";
        ksort($menuList);

        if($request->isPost()){
            $menuSetup = new MenuSetup();
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
                $menuSetup->exchangeArrayFromForm($this->form->getData());
                $menuSetup->menuId = ((int)Helper::getMaxId($this->adapter, MenuSetup::TABLE_NAME, MenuSetup::MENU_ID)) + 1;
                $menuSetup->createdDt = Helper::getcurrentExpressionDate();
                $menuSetup->status='E';
                $this->repository->add($menuSetup);

                $this->flashmessenger()->addMessage("Menu Successfully Added!!!");
                return $this->redirect()->toRoute("menusetup");
            }
        }
        return Helper::addFlashMessagesToArray($this,[
            'form'=>$this->form,
            'menuList'=> $menuList,
            "list"=>$list
        ]);
    }

    public function addAction(){
        $request = $this->getRequest();
        $this->initializeForm();

        $menuList = EntityHelper::getTableKVList($this->adapter,MenuSetup::TABLE_NAME,MenuSetup::MENU_ID,[MenuSetup::MENU_NAME],[MenuSetup::STATUS=>"E"]);
        $menuList[-1]="None";
        ksort($menuList);

        if($request->isPost()){
            $menuSetup = new MenuSetup();
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
                $menuSetup->exchangeArrayFromForm($this->form->getData());
                $menuSetup->menuId = ((int)Helper::getMaxId($this->adapter, MenuSetup::TABLE_NAME, MenuSetup::MENU_ID)) + 1;
                $menuSetup->createdDt = Helper::getcurrentExpressionDate();
                $menuSetup->status='E';
                $this->repository->add($menuSetup);

                $this->flashmessenger()->addMessage("Menu Successfully Added!!!");
                return $this->redirect()->toRoute("menusetup");
            }
        }
        return Helper::addFlashMessagesToArray($this,[
            'form'=>$this->form,
            'menuList'=> $menuList
        ]);
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute("id");
        $this->initializeForm();
        $request = $this->getRequest();

        $menuList = $this->repository->getMenuList($id);

        $menuSetup = new MenuSetup();
        if (!$request->isPost()) {
            $detail = $this->repository->fetchById($id)->getArrayCopy();
            $menuSetup->exchangeArrayFromDB($detail);
            $this->form->bind($menuSetup);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $menuSetup->exchangeArrayFromForm($this->form->getData());
                $menuSetup->modifiedDt = Helper::getcurrentExpressionDate();
                unset($menuSetup->createdDt);
                unset($menuSetup->menuId);
                unset($menuSetup->status);
                $this->repository->edit($menuSetup, $id);
                $this->flashmessenger()->addMessage("Menu Successfully Updated!!!");
                return $this->redirect()->toRoute("menusetup");
            }
        }
        return Helper::addFlashMessagesToArray($this,[
            'id'=>$id,
            'form'=>$this->form,
            'menuList'=> $menuList
        ]);
    }

    public function deleteAction(){
        $id = (int)$this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('menusetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Menu Successfully Deleted!!!");
        return $this->redirect()->toRoute('menusetup');
    }
}