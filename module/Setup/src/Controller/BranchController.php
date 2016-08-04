<?php

namespace Setup\Controller;

use Application\Helper\Helper;
use Setup\Model\BranchRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Setup\Model\Branch;
use Zend\View\View;

class BranchController extends AbstractActionController
{

    private $repository;
    private $branch;
    private $form;

    function __construct(AdapterInterface $adapter)
    {
       print_r($adapter->query('SELECT * FROM `artist` WHERE `id` = ?', [5])) ;
        die();

        $this->repository = new BranchRepository($adapter);
    }


    public function initializeForm()
    {
        $this->branch = new Branch();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($this->branch);
        }
    }

    public function indexAction()
    {
        $branches=$this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this,['branches'=>$branches]);
    }

    public function addAction()
    {
        $this->initializeForm();

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return Helper::addFlashMessagesToArray($this,['form'=>$this->form]);
        }

        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
            $this->branch->exchangeArray($this->form->getData());
            $this->repository->add($this->branch);
            $this->flashmessenger()->addMessage("Branch Successfully Added!!!");
            return $this->redirect()->toRoute("branch");
        } else {
             return Helper::addFlashMessagesToArray($this,['form'=>$this->form]);

        }
    }

    public function editAction()
    {
        $id=(int) $this->params()->fromRoute("id");
        $this->initializeForm();

        $request=$this->getRequest();

        if(!$request->isPost()){
            $this->form->bind($this->repository->fetchById($id));
            return Helper::addFlashMessagesToArray($this,['form'=>$this->form,'id'=>$id]);
        }


        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
            $this->branch->exchangeArray($this->form->getData());
            $this->repository->edit($this->branch,$id);
            $this->flashmessenger()->addMessage("Branch Successfully Updated!!!");
            return $this->redirect()->toRoute("branch");
        } else {
            return Helper::addFlashMessagesToArray($this,['form'=>$this->form,'id'=>$id]);

        }
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Branch Successfully Deleted!!!");
        return $this->redirect()->toRoute('branch');
    }

}