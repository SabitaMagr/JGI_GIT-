<?php

namespace Setup\Controller;

/**
* Master Setup for Designation
* Designation controller.
* Created By: Ukesh Gaiju
* Edited By: Somkala Pachhai
* Date: August 3, 2016, Friday 
* Last Modified By: Somkala Pachhai
* Last Modified Date: August 10,2016, Wednesday 
*/

use Application\Helper\Helper;
use Setup\Form\DesignationForm;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Setup\Entity\HrDesignations;
use Setup\Helper\EntityHelper;
use Doctrine\ORM\EntityManager;

class DesignationController extends AbstractActionController
{
    private $entityManager;
    private $hrDesignations;
    private $designationForm;
    private $hydrator;

    function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->hrDesignations = new HrDesignations();
    }

    public function initializeForm(){
        $form = new DesignationForm();
        $builder = new AnnotationBuilder();
        if(!$this->designationForm){
            $this->designationForm = $builder->createForm($form);
        }
    }

    public function indexAction()
    {
        $designations = $this->entityManager->getRepository(HrDesignations::class)->findAll();
        return Helper::addFlashMessagesToArray($this,["designations" => $designations]);
    }

    public function addAction(){
        
        $this->initializeForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
          
            $this->designationForm->setData($request->getPost());
            if ($this->designationForm->isValid()) {
                $formData = $this->designationForm->getData();
                $this->hrDesignations = EntityHelper::hydrate($this->entityManager,HrDesignations::class,$formData);

                $this->entityManager->persist($this->hrDesignations);
                $this->entityManager->flush();
                
                $this->flashmessenger()->addMessage("Designation Successfully added!!!");
                return $this->redirect()->toRoute("designation");
            } 
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->designationForm,
                'messages' => $this->flashmessenger()->getMessages()
             ]
            )
        );
               
    }

    public function editAction(){
        
        $id=(int) $this->params()->fromRoute("id");
        if($id===0){
            return $this->redirect()->toRoute('designation');
        }
        $this->initializeForm();

        $request=$this->getRequest();

        if(!$request->isPost()){
            $designationRecord = $this->entityManager->find(HrDesignations::class,$id);
            $designationRecord1 = EntityHelper::extract($this->entityManager,$designationRecord);
            $this->designationForm->bind((object)$designationRecord1);
        }else{

            $this->designationForm->setData($request->getPost());
            $modifiedDt = date('Y-m-d');
            if ($this->designationForm->isValid()) {
                $formData = $this->designationForm->getData();
                $newFormData = array_merge($formData,['modifiedDt'=>$modifiedDt]);
                $this->hrDesignations = EntityHelper::hydrate($this->entityManager,HrDesignations::class,$formData);
                $this->hrDesignations->setDesignationId($id);

                $this->entityManager->merge($this->hrDesignations);
                $this->entityManager->flush();

                $this->flashmessenger()->addMessage("Designation Successfully Updated!!!");
                return $this->redirect()->toRoute("designation");
            }
        }
        return Helper::addFlashMessagesToArray(
                    $this,['form'=>$this->designationForm,'id'=>$id]
                 );
    }

    public function deleteAction(){
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('designation');
        }
        $this->hrDesignations =  $this->entityManager->find(HrDesignations::class, $id);
        $this->entityManager->remove($this->hrDesignations);
        $this->entityManager->flush();
        $this->flashmessenger()->addMessage("Designation Successfully Deleted!!!");
        return $this->redirect()->toRoute('designation');
    }


}
/* End of file DesignationController.php */
/* Location: ./Setup/src/Controller/DesignationController.php */
