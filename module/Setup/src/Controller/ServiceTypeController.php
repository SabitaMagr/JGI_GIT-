<?php

namespace Setup\Controller;

/**
* Master Setup for Service Type
* Service Type controller.
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
use Setup\Form\ServiceTypeForm;

use Doctrine\ORM\EntityManager;
use Setup\Entity\HrServiceTypes;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;


class ServiceTypeController extends AbstractActionController{
	
	private $serviceTypeForm;
	private $hydrator;
	private $hrServiceTypes;
	private $entityManager;

	function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->hydrator = new DoctrineHydrator($entityManager);
	}

	private function initializeForm(){
		$form = new ServiceTypeForm();
		$builder = new AnnotationBuilder();
		if (!$this->serviceTypeForm) {
			$this->serviceTypeForm = $builder->createForm($form);
		}
		$this->hrServiceTypes = new HrServiceTypes();
	}

	public function indexAction(){
		$serviceTypeList= $this->entityManager->getRepository(HrServiceTypes::class)->findAll();
		return Helper::addFlashMessagesToArray($this,['serviceTypeList' => $serviceTypeList]);
	}

	public function addAction(){
		
		$this->initializeForm();
        $request = $this->getRequest();

        if (!$request->isPost()) {
        	return Helper::addFlashMessagesToArray($this,[
	            'form' => $this->serviceTypeForm,
	            'messages' => $this->flashmessenger()->getMessages()
        	]);
        }
        $this->serviceTypeForm->setData($request->getPost());

        if ($this->serviceTypeForm->isValid()) {
        	try {
        		$formData = $this->serviceTypeForm->getData();
        		$this->hrServiceTypes = $this->hydrator->hydrate($formData,$this->hrServiceTypes);
	        	$this->entityManager->persist($this->hrServiceTypes);
	        	$this->entityManager->flush();

	            $this->flashmessenger()->addMessage("Service Type Successfully Added!!!");
	            return $this->redirect()->toRoute("serviceType");
	        }
	        catch(Exception $e) {

	        }

        } else {
            return Helper::addFlashMessagesToArray($this,[
	            'form' => $this->serviceTypeForm,
	            'messages' => $this->flashmessenger()->getMessages()
        	]);
        }   
	}

	
	public function editAction(){

		$id=(int) $this->params()->fromRoute("id");
		if($id===0){
			return $this->redirect()->toRoute();
		}
        $this->initializeForm();

        $request=$this->getRequest();

        if(!$request->isPost()){
        	$serviceTypeRecord =(object) $this->entityManager->find(HrServiceTypes::class, $id)->getArrayCopy();		
            $this->serviceTypeForm->bind($serviceTypeRecord);
            return Helper::addFlashMessagesToArray($this,['form'=>$this->serviceTypeForm,'id'=>$id]);
        }

        $modifiedDt = date("Y-m-d");
        $this->serviceTypeForm->setData($request->getPost());

        if ($this->serviceTypeForm->isValid()) {

        	$formData = $this->serviceTypeForm->getData();
        	$newFormData = array_merge($formData,['modifiedDt'=>$modifiedDt]);
        	$this->hrServiceTypes = $this->hydrator->hydrate($newFormData,$this->hrServiceTypes);
        	$this->hrServiceTypes->setServiceTypeId($id);

        	$this->entityManager->merge($this->hrServiceTypes);
        	$this->entityManager->flush();      

            $this->flashmessenger()->addMessage("Service Type Successfully Updated!!!");
            return $this->redirect()->toRoute("serviceType");
        } else {
            return Helper::addFlashMessagesToArray($this,['form'=>$this->serviceTypeForm,'id'=>$id]);

        }
	}
	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
		
		if(!$id){
			return $this->redirect()->toRoute('serviceType');
		}

		$this->hrServiceTypes = $this->entityManager->find(HrServiceTypes::class,$id);
		$this->entityManager->remove($this->hrServiceTypes);
		$this->entityManager->flush();

		$this->flashmessenger()->addMessage("Service Type Successfully Deleted!!!");
		return $this->redirect()->toRoute('serviceType');
	}
}
	
/* End of file ServiceTypeController.php */
/* Location: ./Setup/src/Controller/ServiceTypeController.php */

?>

