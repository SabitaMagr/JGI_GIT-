<?php

namespace Setup\Controller;

/**
* Master Setup for Branch
* Branch controller.
* Created By: Ukesh Gaiju
* Edited By: Somkala Pachhai
* Date: August 3, 2016, Wednesday 
* Last Modified By: Somkala Pachhai
* Last Modified Date: August 10,2016, Wednesday 
*/

use Application\Helper\Helper;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Setup\Form\BranchForm;

use Doctrine\ORM\EntityManager;
use Setup\Entity\HrBranches;
use Setup\Helper\EntityHelper;

class BranchController extends AbstractActionController
{
    private $entityManager;
    private $branchForm;
    private $hydrator;
    private $hrBranches;

    function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->hrBranches = new HrBranches();
    }


    public function initializeForm()
    {
        $form = new BranchForm();
        $builder = new AnnotationBuilder();
        if (!$this->branchForm) {
            $this->branchForm = $builder->createForm($form);
        }     
    }

    public function indexAction()
    {
        $branches = $this->entityManager->getRepository(HrBranches::class)->findAll();
        return Helper::addFlashMessagesToArray($this, ['branches' => $branches]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $this->branchForm->setData($request->getPost());
        
            if ($this->branchForm->isValid()) {
                $formData = $this->branchForm->getData();
                $this->hrBranches = EntityHelper::hydrate($this->entityManager,HrBranches::class,$formData);
        
                $this->entityManager->persist($this->hrBranches);
                $this->entityManager->flush();
               
                $this->flashmessenger()->addMessage("Branch Successfully Added!!!");
                return $this->redirect()->toRoute("branch");
            }
        }
        return Helper::addFlashMessagesToArray($this, ['form' => $this->branchForm]);     
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        $this->initializeForm();

        $request = $this->getRequest();

        if (!$request->isPost()) {
            $branchRecord =$this->entityManager->find(HrBranches::class,$id);
            $branchRecord1 = EntityHelper::extract($this->entityManager,$branchRecord);
            $this->branchForm->bind((object)$branchRecord1);           
        }else{
            $modifiedDt = date('Y-m-d');
            $this->branchForm->setData($request->getPost());

            if ($this->branchForm->isValid()) {

                $formData = $this->branchForm->getData();
                $newFormData = array_merge($formData,['modifiedDt'=>$modifiedDt]);
                $this->hrBranches = EntityHelper::hydrate($this->entityManager,HrBranches::class,$formData);
                $this->hrBranches->setBranchId($id);

                $this->entityManager->merge($this->hrBranches);
                $this->entityManager->flush();

                $this->flashmessenger()->addMessage("Branch Successfully Updated!!!");
                return $this->redirect()->toRoute("branch");
            } 
        }
        return Helper::addFlashMessagesToArray($this, ['form' => $this->branchForm, 'id' => $id]);
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        
        if(!$id){
            return $this->redirect()->toRoute('branch');
        }

        $this->hrBranches = $this->entityManager->find(HrBranches::class,$id);
        $this->entityManager->remove($this->hrBranches);
        $this->entityManager->flush();

        $this->flashmessenger()->addMessage("Branch Successfully Deleted!!!");
        return $this->redirect()->toRoute('branch');
    }

}


/* End of file BranchController.php */
/* Location: ./Setup/src/Controller/BranchController.php */