<?php
namespace Setup\Controller;

/**
* Master Setup for Job History
* Job History controller.
* Created By: Somkala Pachhai
* Edited By:
* Date: August 11, 2016, Thursday 
* Last Modified By: 
* Last Modified Date: 
*/

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Setup\Form\JobHistoryForm;

use Doctrine\ORM\EntityManager;
use Setup\Helper\EntityHelper;
use Setup\Entity\JobHistory;

class JobHistoryController extends AbstractActionController{

	private $entityManager;
	private $jobHistoryForm;
	private $hydrator;
	private $jobHistory;

	public function __construct(EntityManager $entityManager){
		$this->entityManager = $entityManager;
		$this->jobHistory = new JobHistory();
	}

	public function initializeForm(){
		$form = new JobHistoryForm();
		$builder = new AnnotationBuilder();
		if(!$this->jobHistoryForm){
			$this->jobHistoryForm = $builder->createForm($form);
		}
	}

	public function indexAction(){
		$jobHistory = $this->entityManager->getRepository(JobHistory::class)->findAll();
        return Helper::addFlashMessagesToArray($this,['jobHistoryList' => $jobHistory]);
	}

	public function addAction(){
		$this->initializeForm();
		$request = $this->getRequest();

		if($request->isPost()){
		
			$this->jobHistoryForm->setData($request->getPost());
	        if ($this->jobHistoryForm->isValid()) {    
	            $formData = $this->jobHistoryForm->getData();  
	            $this->jobHistory = EntityHelper::hydrate($this->entityManager,JobHistory::class,$formData); 

	            $this->entityManager->persist($this->jobHistory);
	            $this->entityManager->flush();  

	            $this->flashmessenger()->addMessage("Job History Successfully added!!!");
	            return $this->redirect()->toRoute("jobHistory");
	        } 
    	}
        return Helper::addFlashMessagesToArray(
			$this,
			[
				'form'=>$this->jobHistoryForm,
				'messages' => $this->flashmessenger()->getMessages(),
				'departments'=>EntityHelper::getDepartmentKVList($this->entityManager),
				'designations'=>EntityHelper::getDesignationKVList($this->entityManager),
				'branches'=>EntityHelper::getBranchKVList($this->entityManager),
				'positions'=>EntityHelper::getPositionKVList($this->entityManager),
				'serviceTypes'=>EntityHelper::getServiceTypeKVList($this->entityManager),
			]
		);
	}

	public function editAction(){
		$id=(int) $this->params()->fromRoute("id");
		if($id===0){
			return $this->redirect()->toRoute('jobHistory');
		}
        $this->initializeForm();
        $request=$this->getRequest();

        if(!$request->isPost()){
            $jobHistoryRecord = $this->entityManager->find(JobHistory::class,$id);
            $jobHistoryRecord1 = EntityHelper::extract($this->entityManager,$jobHistoryRecord);
            $this->jobHistoryForm->bind((object)$jobHistoryRecord1);
        }else{

            $this->jobHistoryForm->setData($request->getPost());
            $modifiedDt = date('Y-m-d');
            if ($this->jobHistoryForm->isValid()) {
                $formData = $this->jobHistoryForm->getData();
                $newFormData = array_merge($formData,['modifiedDt'=>$modifiedDt]);
                $this->jobHistory = EntityHelper::hydrate($this->entityManager,JobHistory::class,$formData); 
                $this->jobHistory->setId($id);

                $this->entityManager->merge($this->jobHistory);
                $this->entityManager->flush();

                $this->flashmessenger()->addMessage("Job History Successfully Updated!!!");
                return $this->redirect()->toRoute("jobHistory");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,
            [
            	'form'=>$this->jobHistoryForm,
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
            return $this->redirect()->toRoute('jobHistory');
        }
        $this->jobHistory =  $this->entityManager->find(JobHistory::class, $id);
        $this->entityManager->remove($this->jobHistory);
        $this->entityManager->flush();
        $this->flashmessenger()->addMessage("Job History Successfully Deleted!!!");
        return $this->redirect()->toRoute("jobHistory");
    }
	
}

/* End of file JobHistoryController.php */
/* Location: ./Setup/src/Controller/JobHistoryController.php */