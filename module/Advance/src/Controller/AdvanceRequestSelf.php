<?php

namespace Advance\Controller;

use Advance\Form\AdvanceRequestForm;
use Advance\Repository\AdvanceRequestSelfRepository;
use Application\Controller\HrisController;
use Application\Helper\Helper;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;

class AdvanceRequestSelf extends HrisController {

    private $repository;

//    private $form;
//    private $recommender;
//    private $approver;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->repository = new AdvanceRequestSelfRepository($adapter);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $attendanceRequest = new AdvanceRequestForm();
        $this->form = $builder->createForm($attendanceRequest);
    }

    public function indexAction() {


        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl
        ]);
    }

    public function addAction() {
        
    }

    public function viewAction() {
        
    }

}
