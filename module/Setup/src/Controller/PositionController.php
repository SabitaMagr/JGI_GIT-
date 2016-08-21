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
use Setup\Model\PositionRepository;
use Zend\Db\Adapter\AdapterInterface;



class PositionController extends AbstractActionController
{

    private $positionForm;
    private $hrPositions;
    private $hydrator;
    private $repository;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new PositionRepository($adapter);
    }

     public function initializeForm()
    {   
        $form = new PositionForm();
        $builder = new AnnotationBuilder();
        if (!$this->positionForm) {
            $this->positionForm = $builder->createForm($form);
        }
    }

    public function indexAction()
    {
        $positionList  = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this,['positions' => $positionList]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            
            $this->positionForm->setData($request->getPost());      
            if ($this->positionForm->isValid()) {    
                $formData = $this->positionForm->getData();  
                $this->hrPositions = EntityHelper::hydrate($this->entityManager,HrPositions::class,$formData); 

                $this->entityManager->persist($this->hrPositions);
                $this->entityManager->flush();  

                $this->flashmessenger()->addMessage("Position Successfully added!!!");
                return $this->redirect()->toRoute("position");
            } 
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                $this,
                [
                    'form' => $this->positionForm,
                    'messages' => $this->flashmessenger()->getMessages()
                 ]
                )
            );   
    }

    // public function editAction()
    // {
    //     $id=(int) $this->params()->fromRoute("id");
    //     if($id===0){
    //         return $this->redirect()->toRoute('position');
    //     }
    //     $this->initializeForm();
    //     $request=$this->getRequest();

    //     $modifiedDt = date("Y-m-d");
    //     if(!$request->isPost()){
    //         $positionRecord = $this->entityManager->find(HrPositions::class, $id);
    //         $positionRecord1  = EntityHelper::extract($this->entityManager,$positionRecord);
    //         $this->positionForm->bind((object)$positionRecord1);
    //     }else{

    //         $this->positionForm->setData($request->getPost());
    //         if ($this->positionForm->isValid()) {

    //             $formData = $this->positionForm->getData();
    //             $newFormData =  array_merge($formData, ['modifiedDt'=> $modifiedDt ]);        
    //             $this->hrPositions = EntityHelper::hydrate($this->entityManager,HrPositions::class,$formData);   
    //             $this->hrPositions->setPositionId($id);

    //             $this->entityManager->merge($this->hrPositions);
    //             $this->entityManager->flush();     
                
    //             $this->flashmessenger()->addMessage("Position Successfully Updated!!!");
    //             return $this->redirect()->toRoute("position");
    //         }
    //     }
    //     return Helper::addFlashMessagesToArray(
    //         $this,['form'=>$this->positionForm,'id'=>$id]
    //      );
       
    // }

    // public function deleteAction()
    // {
    //     $id = (int)$this->params()->fromRoute("id");
    //     if (!$id) {
    //         return $this->redirect()->toRoute('position');
    //     }
    //     $this->hrPositions =  $this->entityManager->find(HrPositions::class, $id);
    //     $this->entityManager->remove($this->hrPositions);
    //     $this->entityManager->flush();
    //     $this->flashmessenger()->addMessage("Position Successfully Deleted!!!");
    //     return $this->redirect()->toRoute('position');
    // }    
 
}

/* End of file PositionController.php */
/* Location: ./Setup/src/Controller/PositionController.php */
?>