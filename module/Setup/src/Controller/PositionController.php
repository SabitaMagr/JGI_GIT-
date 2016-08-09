<?php

namespace Setup\Controller;

use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Model\Position;
use Setup\Model\PositionRepository;

use Doctrine\ORM\EntityManager;
use Setup\Entity\HrPositions;

class PositionController extends AbstractActionController
{

    // private $repository;
    private $form;
    private $position;
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

    }

     public function initializeForm()
    {
        $this->position = new Position();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($this->position);
        }
    }

    public function indexAction()
    {
        $this->position  = $this->entityManager->getRepository('Setup\Entity\HrPositions')->findAll();
        return Helper::addFlashMessagesToArray($this,['positions' => $this->position]);
    }

    

    public function addAction()
    {
        $this->initializeForm();
        $hrPosition = new HrPositions();
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return new ViewModel(Helper::addFlashMessagesToArray(
                $this,
                [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages()
                 ]
                )
            );
        }

        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {      
            $hrPosition->exchangeArray($this->form->getData());
            $this->entityManager->persist($hrPosition);
            $this->entityManager->flush();  

            $this->flashmessenger()->addMessage("Position Successfully added!!!");
            return $this->redirect()->toRoute("position");
        } else {
                return new ViewModel(Helper::addFlashMessagesToArray(
                $this,
                [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages()
                 ]
                )
            );
        }   
    }


    public function editAction()
    {
        $id=(int) $this->params()->fromRoute("id");
        if($id===0){
            return $this->redirect()->toRoute('position');
        }
        $this->initializeForm();

        $request=$this->getRequest();

        $hrPosition1 = new HrPositions();

        $modifiedDt = date("Y-m-d");
        if(!$request->isPost()){
            $hrPosition = $this->entityManager->find('Setup\Entity\HrPositions', $id);
            
            //print_r($hrPosition1->getArrayCopy()); die();
            //print_r($hrPosition); die();

            $this->form->bind($hrPosition);

            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->form,'id'=>$id]
                );
        }

        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
            $this->position->exchangeArrayFromForm($this->form->getData());
            $this->repository->edit($this->position,$id,$modifiedDt);
            $this->flashmessenger()->addMessage("Position Successfully Updated!!!");
           return $this->redirect()->toRoute("position");
        } else {
            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->form,'id'=>$id]
             );

        }

    }



    // public function deleteAction()
    // {
    //     $id = (int)$this->params()->fromRoute("id");
    //     $this->repository->delete($id);
    //     $this->flashmessenger()->addMessage("Position Successfully Deleted!!!");
    //     return $this->redirect()->toRoute('position');
    // }

 
}


?>