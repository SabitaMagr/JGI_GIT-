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
use Setup\Helper\EntityHelper;

class ServiceTypeController extends AbstractActionController{
	
	private $serviceTypeForm;
	private $hydrator;
	private $hrServiceTypes;
	private $entityManager;

	function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->hrServiceTypes = new HrServiceTypes();
	}

	private function initializeForm(){
		$form = new ServiceTypeForm();
		$builder = new AnnotationBuilder();
		if (!$this->serviceTypeForm) {
			$this->serviceTypeForm = $builder->createForm($form);
		}
	}

	public function indexAction(){
		$serviceTypeList= $this->entityManager->getRepository(HrServiceTypes::class)->findAll();
		return Helper::addFlashMessagesToArray($this,['serviceTypeList' => $serviceTypeList]);
	}

	public function addAction(){
		
		$this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
        
	        $this->serviceTypeForm->setData($request->getPost());
	        if ($this->serviceTypeForm->isValid()) {
	        	try {
	        		$formData = $this->serviceTypeForm->getData();
	        		$this->hrServiceTypes = EntityHelper::hydrate($this->entityManager,HrServiceTypes::class,$formData);
		        	$this->entityManager->persist($this->hrServiceTypes);
		        	$this->entityManager->flush();

		            $this->flashmessenger()->addMessage("Service Type Successfully Added!!!");
		            return $this->redirect()->toRoute("serviceType");
		        }
		        catch(Exception $e) {

		        }
	        }
    	}
        return Helper::addFlashMessagesToArray($this,[
            'form' => $this->serviceTypeForm,
            'messages' => $this->flashmessenger()->getMessages()
    	]);     
	}

	
	public function editAction(){

		$id=(int) $this->params()->fromRoute("id");
		if($id===0){
			return $this->redirect()->toRoute();
		}
        $this->initializeForm();

        $request=$this->getRequest();

        if(!$request->isPost()){
        	$serviceTypeRecord = $this->entityManager->find(HrServiceTypes::class, $id);
        	$serviceTypeRecord1 = EntityHelper::extract($this->entityManager,$serviceTypeRecord);
            $this->serviceTypeForm->bind((object)$serviceTypeRecord1);
        }else{

	        $modifiedDt = date("Y-m-d");
	        $this->serviceTypeForm->setData($request->getPost());

	        if ($this->serviceTypeForm->isValid()) {

	        	$formData = $this->serviceTypeForm->getData();
	        	$newFormData = array_merge($formData,['modifiedDt'=>$modifiedDt]);
	        	$this->hrServiceTypes = EntityHelper::hydrate($this->entityManager,HrServiceTypes::class,$formData);
	        	$this->hrServiceTypes->setServiceTypeId($id);

	        	$this->entityManager->merge($this->hrServiceTypes);
	        	$this->entityManager->flush();      

	            $this->flashmessenger()->addMessage("Service Type Successfully Updated!!!");
	            return $this->redirect()->toRoute("serviceType");
	        }
    	}
        return Helper::addFlashMessagesToArray($this,['form'=>$this->serviceTypeForm,'id'=>$id]);
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

