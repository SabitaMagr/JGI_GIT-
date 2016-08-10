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

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class PositionController extends AbstractActionController
{

    // private $repository;
    private $form;
    private $positionForm;
    private $entityManager;
    private $hrPosition;
    private $hydrator;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->hydrator = new DoctrineHydrator($entityManager);

    }

     public function initializeForm()
    {   
        $this->hrPosition = new HrPositions();
        $this->positionForm = new Position();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($this->positionForm);
        }
    }

    public function indexAction()
    {
        $positionList  = $this->entityManager->getRepository('Setup\Entity\HrPositions')->findAll();
        return Helper::addFlashMessagesToArray($this,['positions' => $positionList]);
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
            $positionEdit = (object)$this->entityManager->find('Setup\Entity\HrPositions', $id)->getArrayCopy();
            $this->form->bind($positionEdit);
            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->form,'id'=>$id]
                );
        }

        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {

            $form = $this->form->getData();
            $this->hrPosition = $this->hydrator->hydrate($form, $this->hrPosition);  
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