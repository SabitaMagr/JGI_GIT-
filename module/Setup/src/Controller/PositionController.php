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
    private $hrPosition;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

    }

     public function initializeForm()
    {   
        $this->hrPosition = new HrPositions();
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
            $this->hrPosition->exchangeArray($this->form->getData());
            $this->entityManager->persist($this->hrPosition);
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

        $modifiedDt = date("Y-m-d");
        if(!$request->isPost()){
            $this->hrPosition->exchangeArray($this->entityManager->find('Setup\Entity\HrPositions', $id)->getArrayCopy());

            $this->form->bind((object)$this->hrPosition->getArrayCopy());

            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->form,'id'=>$id]
                );
        }

        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
            $this->hrPosition->exchangeArray($this->form->getData());
            $this->hrPosition->setPositionId($id);
            $this->entityManager->merge($this->hrPosition);
            $this->entityManager->flush();     
            
            $this->flashmessenger()->addMessage("Position Successfully Updated!!!");
            return $this->redirect()->toRoute("position");
        } else {
            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->form,'id'=>$id]
             );
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->hrPosition =  $this->entityManager->find('Setup\Entity\HrPositions', $id);

        $this->entityManager->remove($this->hrPosition);
        $this->entityManager->flush();
        $this->flashmessenger()->addMessage("Position Successfully Deleted!!!");
        return $this->redirect()->toRoute('position');
    }
 
}


?>