<?php
namespace Setup\Controller;

/**
* Master Setup for Leave Type
* Leave Type controller.
* Created By: Somkala Pachhai
* Edited By: Somkala Pachhai
* Date: August 3, 2016, Wednesday 
* Last Modified By: Somkala Pachhai
* Last Modified Date: August 10,2016, Wednesday 
*/

use Application\Helper\Helper;
use Zend\View\Model\ViewModel;
use Setup\Form\LeaveTypeForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;

use Doctrine\ORM\EntityManager;
use Setup\Entity\HrLeaveTypes;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class LeaveTypeController extends AbstractActionController {

	private $leaveTypeForm;
	private $entityManager;
	private $hydrator;
	private $hrLeaveTypes;

	public function __construct(EntityManager $entityManager){

		$this->hydrator = new DoctrineHydrator($entityManager);
		$this->entityManager =  $entityManager;
		$this->hrLeaveTypes =  new HrLeaveTypes();
	}

	public function initializeForm(){

		$form = new LeaveTypeForm();
		$builder =  new AnnotationBuilder();	
		$this->leaveTypeForm = $builder -> createForm($form);
	}

	public function indexAction(){
		$leaveTypeList = $this->entityManager->getRepository(HrLeaveTypes::class)->findAll();
		return Helper::addFlashMessagesToArray($this,['leaveTypeList'=> $leaveTypeList]);
	}

	public function addAction(){
		$this->initializeForm();
		$request = $this->getRequest();
		if(!$request->isPost()){
			return Helper::addFlashMessagesToArray(
				$this,
				[
					'form'=>$this->leaveTypeForm,
					'messages' => $this->flashmessenger()->getMessages()
				]
			);
		}
		$this->leaveTypeForm->setData($request->getPost());

		if($this->leaveTypeForm->isValid()){

			$formData = $this->leaveTypeForm->getData();
			$this->hrLeaveTypes = $this->hydrator->hydrate($formData,$this->hrLeaveTypes);
			$this->entityManager->persist($this->hrLeaveTypes);
			$this->entityManager->flush();

			$this->flashmessenger()->addMessage("Leave Type Successfully Added!!!");
			return $this->redirect()->toRoute("leaveType");

		}else{

			return Helper::addFlashMessagesToArray($this,['form'=>$this->leaveTypeForm]);
		}
	}

	public function editAction()
	{
	   
	    $id=(int) $this->params()->fromRoute("id");
        if($id===0){
            return $this->redirect()->toRoute('leaveType');
        }
        $this->initializeForm();

        $request=$this->getRequest();
        $modifiedDt = date("Y-m-d");
        if(!$request->isPost()){
        	$leaveTypeRecord = (object)$this->entityManager->find(HrLeaveTypes::class,$id)->getArrayCopy();
            $this->leaveTypeForm->bind($leaveTypeRecord);
            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->leaveTypeForm,'id'=>$id]
                );
        }

        $this->leaveTypeForm->setData($request->getPost());

        if ($this->leaveTypeForm->isValid()) {

        	$formData =  $this->leaveTypeForm->getData();
            $newFormData = array_merge($formData,['modifiedDt'=>$modifiedDt]);
        	$this->hrLeaveTypes = $this->hydrator->hydrate($formData,$this->hrLeaveTypes);
        	$this->hrLeaveTypes->setLeaveId($id);

        	$this->entityManager->merge($this->hrLeaveTypes);
        	$this->entityManager->flush();

            $this->flashmessenger()->addMessage("Leave Type Successfully Updated!!!");
           return $this->redirect()->toRoute("leaveType");

        } else {
            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->leaveTypeForm,'id'=>$id]
             );

        }
          
	}

	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->hrLeaveTypes =  $this->entityManager->find(HrLeaveTypes::class, $id);
        $this->entityManager->remove($this->hrLeaveTypes);
        $this->entityManager->flush();

        $this->flashmessenger()->addMessage("Leave Type Successfully Deleted!!!");
        return $this->redirect()->toRoute('leaveType');
	}

}

/* End of file LeaveTypeController.php */
/* Location: ./Setup/src/Controller/LeaveTypeController.php */