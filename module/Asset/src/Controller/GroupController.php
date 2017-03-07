<?php

namespace Asset\Controller;

use Application\Helper\Helper;
use Asset\Form\GroupForm;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class GroupController extends AbstractActionController {

//    private $adapter;
//    private $repository;
//    private $form;
//    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
//        $this->adapter = $adapter;
//        $this->repository = new HeadingRepository($adapter);
    }

    public function initializeForm() {
        $form = new GroupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        return [
            'a' => 'sdf'
        ];
    }

    public function addAction() {
        $this->initializeForm();

        return[
            'form' => $this->form,
        ];
    }

}
