<?php

namespace Setup\Controller;

use Setup\Model\DesignationRepositoryInterface;
use Setup\Model\EmployeeRepositoryInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DesignationController extends AbstractActionController
{
    private $repository;

    function __construct(DesignationRepositoryInterface $repository)
    {
          $this->repository=$repository;
    }


    public function indexAction()
    {
        $designations=$this->repository->fetchAll();
        return new ViewModel(["designations"=>$designations]);
    }

    public function editAction()
    {

    }

    public function addAction()
    {

    }

}
