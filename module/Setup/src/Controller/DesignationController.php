<?php

namespace Setup\Controller;

use Application\Helper\Helper;
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
        return Helper::addFlashMessagesToArray($this,["designations" => $designations]);
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
            return Helper::addFlashMessagesToArray($this,["form" => $designationForm, "id" => $id]);
        }

        $designationForm->setData($request->getPost());
        if ($designationForm->isValid()) {
            $designation->exchangeArray($designationForm->getData());
            $this->repository->editDesignation($designation, $id);
            $this->flashmessenger()->addMessage("Deisgnation Successfully Updated!!!");
           return $this->redirect()->toRoute("designation");
        } else {
            return Helper::addFlashMessagesToArray($this,["form" => $designationForm, "id" => $id]);

        }

    }

    public function addAction()
    {
        $designation = new Designation();
        $builder = new AnnotationBuilder();

        $designationForm = $builder->createForm($designation);

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return Helper::addFlashMessagesToArray($this,["form" => $designationForm]);
        }

        $designationForm->setData($request->getPost());

        if ($designationForm->isValid()) {
            $designation->exchangeArray($designationForm->getData());
            $this->repository->addDesignation($designation);
            $this->flashmessenger()->addMessage("Deisgnation Successfully Added!!!");
            return $this->redirect()->toRoute("designation");
        } else {
            return Helper::addFlashMessagesToArray($this,["form" => $designationForm]);

        }


    }

}
