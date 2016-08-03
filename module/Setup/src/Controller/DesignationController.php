<?php

namespace Setup\Controller;


use Setup\Model\Designation;
use Setup\Model\DesignationRepositoryInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DesignationController extends AbstractActionController
{
    private $repository;

    function __construct(DesignationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    public function indexAction()
    {
        $designations = $this->repository->fetchAll();
        return new ViewModel(["designations" => $designations]);
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute("id", 0);

        if ($id === 0) {
            $this->redirect()->toRoute("designation");
        }

        $designation = new Designation();
        $builder = new AnnotationBuilder();
        $designationForm = $builder->createForm($designation);

        $request = $this->getRequest();

        if (!$request->isPost()) {
            $designationForm->bind($this->repository->fetchById($id));
            return ["form"=>$designationForm,"id"=>$id];
        }

        $designationForm->setData($request->getPost());
        if($designationForm->isValid()){
            $designation->exchangeArray($designationForm->getData());
            $this->repository->editDesignation($designation,$id);
            $this->redirect()->toRoute("designation",["action"=>"edit"]);
        }else{
            $this->redirect()->toRoute("designation",["action"=>"edit"]);

        }

    }

    public function addAction()
    {
        $designation = new Designation();
        $builder = new AnnotationBuilder();

        $designationForm = $builder->createForm($designation);

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return ['form' => $designationForm];
        }

        $designationForm->setData($request->getPost());

        if ($designationForm->isValid()) {
            $designation->exchangeArray($designationForm->getData());
            $this->repository->addDesignation($designation);
            $this->redirect()->toRoute("designation");
        } else {
            $this->redirect()->toRoute("designation", ["action" => "add"]);

        }


    }

}
