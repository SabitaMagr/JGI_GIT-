<?php

namespace Setup\Controller;

/**
* Master Setup for Position
* Position controller.
* Created By: Somkala Pachhai
* Edited By: Somkala Pachhai
* Date: August 2, 2016, Wednesday 
* Last Modified By: Somkala Pachhai
* Last Modified Date: August 10,2016, Wednesday 
*/


use Application\Helper\Helper;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Form\PositionForm;

use Doctrine\ORM\EntityManager;
use Setup\Entity\HrPositions;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class PositionController extends AbstractActionController
{

    private $positionForm;
    private $entityManager;
    private $hrPositions;
    private $hydrator;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->hydrator = new DoctrineHydrator($entityManager);

    }

     public function initializeForm()
    {   
        $this->hrPositions = new HrPositions();
        $form = new PositionForm();
        $builder = new AnnotationBuilder();
        if (!$this->positionForm) {
            $this->positionForm = $builder->createForm($form);
        }
    }

    public function indexAction()
    {
        $positionList  = $this->entityManager->getRepository(HrPositions::class)->findAll();
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
                    'form' => $this->positionForm,
                    'messages' => $this->flashmessenger()->getMessages()
                 ]
                )
            );
        }

        $this->positionForm->setData($request->getPost());

        if ($this->positionForm->isValid()) {    
            $formData = $this->positionForm->getData();  
            $this->hrPositions = $this->hydrator->hydrate($formData, $this->hrPositions); 

            $this->entityManager->persist($this->hrPositions);
            $this->entityManager->flush();  

            $this->flashmessenger()->addMessage("Position Successfully added!!!");
            return $this->redirect()->toRoute("position");
        } else {
                return new ViewModel(Helper::addFlashMessagesToArray(
                $this,
                [
                    'form' => $this->positionForm,
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
            $positionRecord = (object)$this->entityManager->find(HrPositions::class, $id)->getArrayCopy();
            $this->positionForm->bind($positionRecord);
            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->positionForm,'id'=>$id]
                );
        }

        $this->positionForm->setData($request->getPost());

        if ($this->positionForm->isValid()) {

            $formData = $this->positionForm->getData();
            $newFormData =  array_merge($formData, ['modifiedDt'=> $modifiedDt ]);        
            $this->hrPositions = $this->hydrator->hydrate($newFormData, $this->hrPositions);  
            $this->hrPositions->setPositionId($id);

            $this->entityManager->merge($this->hrPositions);
            $this->entityManager->flush();     
            
            $this->flashmessenger()->addMessage("Position Successfully Updated!!!");
            return $this->redirect()->toRoute("position");
        } else {
            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->positionForm,'id'=>$id]
             );
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->hrPositions =  $this->entityManager->find(HrPositions::class, $id);
        $this->entityManager->remove($this->hrPositions);
        $this->entityManager->flush();
        $this->flashmessenger()->addMessage("Position Successfully Deleted!!!");
        return $this->redirect()->toRoute('position');
    }


    
 
}


/* End of file PositionController.php */
/* Location: ./Setup/src/Controller/PositionController.php */
?>