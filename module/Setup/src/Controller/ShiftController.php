<?php 

namespace Setup\Controller;

/**
* Master Setup for Shift
* Shift controller.
* Created By: Somkala Pachhai
* Edited By: Somkala Pachhai
* Date: August 5, 2016, Friday 
* Last Modified By: Somkala Pachhai
* Last Modified Date: August 10,2016, Wednesday 
*/

use Application\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use Setup\Form\ShiftForm;

use Doctrine\ORM\EntityManager;
use Setup\Entity\HrShifts;
use Setup\Helper\EntityHelper;

class ShiftController extends AbstractActionController {
	
	private $hrShifts;
	private $shiftForm;
	private $entityManager;
	private $hydrator;

	public function __construct(EntityManager $entityManager){
		$this->entityManager = $entityManager;
		$this->hrShifts = new HrShifts();
	}

	public function indexAction(){
		$shiftList = $this->entityManager->getRepository(HrShifts::class)->findAll();
		return Helper::addFlashMessagesToArray($this,['shiftList'=>$shiftList]);
	}

	public function initializeForm(){
		$form = new ShiftForm();
		$builder = new AnnotationBuilder();
		$this->shiftForm = $builder->createForm($form);
	}

	public function addAction(){
		
		$this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
        
	        $this->shiftForm->setData($request->getPost());
	        if ($this->shiftForm->isValid()) {

	        	$formData = $this->shiftForm->getData();
	        	$this->hrShifts = EntityHelper::hydrate($this->entityManager,HrShifts::class,$formData);

	            $this->entityManager->persist($this->hrShifts);
	            $this->entityManager->flush();
	            
	            $this->flashmessenger()->addMessage("Shift Successfully added!!!");
	            return $this->redirect()->toRoute("shift");
	        } 
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->shiftForm,
                'messages' => $this->flashmessenger()->getMessages()
             ]
            )
        );
   	}

	public function editAction(){
		$this->initializeForm();
		$id = (int) $this->params()->fromRoute("id");

		if($id===0){
			return $this->redirect()->toRoute("shift");
		}

		$request = $this->getRequest();
		$modifiedDt = date("Y-m-d");

		if(!$request->isPost()){
			
			$shiftRecord = $this->entityManager->find(HrShifts::class,$id);
			$shiftRecord1 = EntityHelper::extract($this->entityManager,$shiftRecord);
			$this->shiftForm->bind((object)$shiftRecord1);
		}else{

			$this->shiftForm->setData($request->getPost());
			if($this->shiftForm->isValid()){

				$formData = $this->shiftForm->getData();
				$newFormData = array_merge($formData,['modifiedDt'=>$modifiedDt]);
				$this->hrShifts = EntityHelper::hydrate($this->entityManager,HrShifts::class,$formData);
				$this->hrShifts->setShiftId($id);
				
				$this->entityManager->merge($this->hrShifts);
				$this->entityManager->flush();
			
				$this->flashmessenger()->addMessage("Shift Successfuly Updated!!!");
				return $this->redirect()->toRoute("shift");
			}
		}
		return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->shiftForm,
                'id'=>$id
             ]
            )
        );
	}

	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->hrShifts =  $this->entityManager->find(HrShifts::class, $id);
        $this->entityManager->remove($this->hrShifts);
        $this->entityManager->flush();

        $this->flashmessenger()->addMessage("Shift Successfully Deleted!!!");
        return $this->redirect()->toRoute('shift');
	}
}


/* End of file ShiftController.php */
/* Location: ./Setup/src/Controller/ShiftController.php */