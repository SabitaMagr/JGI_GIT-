<?php
namespace Setup\Controller;

/**
* Master Setup for Employee Current Posting
* Employee Current Posting controller.
* Created By: Somkala Pachhai
* Edited By: 
* Date: August 12, 2016, Friday 
* Last Modified By: 
* Last Modified Date: 
*/


use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Setup\Form\EmpCurrentPostingForm;

use Doctrine\ORM\EntityManager;
use Setup\Helper\EntityHelper;
use Setup\Entity\EmployeeCurrentPosting;

class EmpCurrentPostingController extends AbstractActionController{

	private $entityManager;
	private $empCurrentPostingForm;
	private $hydrator;
	private $empCurrentPosting;

	public function __construct(EntityManager $entityManager){
		$this->entityManager = $entityManager;
		$this->empCurrentPosting = new EmployeeCurrentPosting();
	}

	public function initializeForm(){
		$form = new EmpCurrentPostingForm();
		$builder = new AnnotationBuilder();
		if(!$this->empCurrentPostingForm){
			$this->empCurrentPostingForm = $builder->createForm($form);
		}
	}

	public function indexAction(){
		$empCurrentPostingList = $this->entityManager->getRepository(EmployeeCurrentPosting::class)->findAll();
        return Helper::addFlashMessagesToArray($this,['empCurrentPostingList' => $empCurrentPostingList]);
	}

	public function addAction(){
		$this->initializeForm();
		$request = $this->getRequest();

		if($request->isPost()){
		
			$this->empCurrentPostingForm->setData($request->getPost());
			if($this->empCurrentPostingForm->isValid()){
				$formData = $this->empCurrentPostingForm->getData();
				$this->empCurrentPosting = EntityHelper::hydrate($this->entityManager,EmployeeCurrentPosting::class,$formData);
				$this->entityManager->persist($this->empCurrentPosting);
				$this->entityManager->flush();

				$this->flashmessenger()->addMessage("Employee Current Posting Successfully added!!!");
	            return $this->redirect()->toRoute("empCurrentPosting");
			}
		}
		return Helper::addFlashMessagesToArray(
			$this,
			[
				'form'=>$this->empCurrentPostingForm,
				'messages' => $this->flashmessenger()->getMessages(),
				'departments'=>EntityHelper::getDepartmentKVList($this->entityManager),
				'designations'=>EntityHelper::getDesignationKVList($this->entityManager),
				'branches'=>EntityHelper::getBranchKVList($this->entityManager),
				'positions'=>EntityHelper::getPositionKVList($this->entityManager),
				'serviceTypes'=>EntityHelper::getServiceTypeKVList($this->entityManager),
			]);
	}

	public function editAction(){
		$id=(int) $this->params()->fromRoute("id");
		if($id===0){
			return $this->redirect()->toRoute('empCurrentPosting');
		}
        $this->initializeForm();
        $request=$this->getRequest();

        if(!$request->isPost()){
            $empCurrentPostDtl = $this->entityManager->find(EmployeeCurrentPosting::class,$id);
            $empCurrentPostDtl1 = EntityHelper::extract($this->entityManager,$empCurrentPostDtl);
            $this->empCurrentPostingForm->bind((object)$empCurrentPostDtl1);
        }else{

            $this->empCurrentPostingForm->setData($request->getPost());
            $modifiedDt = date('Y-m-d');
            if ($this->empCurrentPostingForm->isValid()) {
                $formData = $this->empCurrentPostingForm->getData();
                $newFormData = array_merge($formData,['modifiedDt'=>$modifiedDt]);
                $this->empCurrentPosting = EntityHelper::hydrate($this->entityManager,EmployeeCurrentPosting::class,$formData);
                $this->empCurrentPosting->setId($id);

                $this->entityManager->merge($this->empCurrentPosting);
                $this->entityManager->flush();

                $this->flashmessenger()->addMessage("Employee Current Posting Updated!!!");
                return $this->redirect()->toRoute("empCurrentPosting");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,
            [
            	'form'=>$this->empCurrentPostingForm,
            	'id'=>$id,
            	'departments'=>EntityHelper::getDepartmentKVList($this->entityManager),
				'designations'=>EntityHelper::getDesignationKVList($this->entityManager),
				'branches'=>EntityHelper::getBranchKVList($this->entityManager),
				'positions'=>EntityHelper::getPositionKVList($this->entityManager),
				'serviceTypes'=>EntityHelper::getServiceTypeKVList($this->entityManager),      
            ]
        );
	}

	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('empCurrentPosting');
        }
        $this->empCurrentPosting =  $this->entityManager->find(EmployeeCurrentPosting::class, $id);
        $this->entityManager->remove($this->empCurrentPosting);
        $this->entityManager->flush();
        $this->flashmessenger()->addMessage("Employee Current Posting Deleted!!!");
        return $this->redirect()->toRoute("empCurrentPosting");
	}
}

/* End of file EmpCurrentPostingController.php */
/* Location: ./Setup/src/Controller/EmpCurrentPostingController.php */