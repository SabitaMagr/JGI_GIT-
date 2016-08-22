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
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\JobHistoryRepository;
use Setup\Helper\EntityHelper;

class JobHistoryController extends AbstractActionController{

	private $jobHistory;
	private $repository;
	private $form;
	private $adapter;

	public function __construct(AdapterInterface $adapter){
		$this->repository = new JobHistoryRepository($adapter);
		$this->adapter = $adapter;
	}

	public function initializeForm(){
		$this->jobHistory = new JobHistoryForm();
		$builder = new AnnotationBuilder();
		if(!$this->form){
			$this->form = $builder->createForm($this->jobHistory);
		}
	}

	public function indexAction(){
		$jobHistory = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this,['jobHistoryList' => $jobHistory]);
	}

	public function addAction(){
		$this->initializeForm();
		$request = $this->getRequest();

		if($request->isPost()){
		
			$this->form->setData($request->getPost());
	        if ($this->form->isValid()) { 

	           	$this->jobHistory->exchangeArrayFromForm($this->form->getData());
	           	$this->repository->add($this->jobHistory);
	            $this->flashmessenger()->addMessage("Job History Successfully added!!!");
	            return $this->redirect()->toRoute("jobHistory");
	        } 
    	}
        return Helper::addFlashMessagesToArray(
			$this,
			[
				'form'=>$this->form,
				'messages' => $this->flashmessenger()->getMessages(),
				'departments'=>EntityHelper::getDepartmentKVList($this->adapter),
				'designations'=>EntityHelper::getDesignationKVList($this->adapter),
				'branches'=>EntityHelper::getBranchKVList($this->adapter),
				'positions'=>EntityHelper::getPositionKVList($this->adapter),
				'serviceTypes'=>EntityHelper::getServiceTypeKVList($this->adapter),
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
          $this->jobHistory->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
          $this->form->bind((object)$this->jobHistory->getArrayCopyForDb());
        }else{

            $this->form->setData($request->getPost());           
            if ($this->form->isValid()) {
                
                $this->jobHistory->exchangeArrayFromForm($this->form->getData());
                $this->repository->edit($this->jobHistory,$id);
                $this->flashmessenger()->addMessage("Job History Successfully Updated!!!");
                return $this->redirect()->toRoute("jobHistory");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,
            [
            	'form'=>$this->form,
            	'id'=>$id,
				'messages' => $this->flashmessenger()->getMessages(),
				'departments'=>EntityHelper::getDepartmentKVList($this->adapter),
				'designations'=>EntityHelper::getDesignationKVList($this->adapter),
				'branches'=>EntityHelper::getBranchKVList($this->adapter),
				'positions'=>EntityHelper::getPositionKVList($this->adapter),
				'serviceTypes'=>EntityHelper::getServiceTypeKVList($this->adapter), 
            ]
        );
	}

	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('jobHistory');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Job History Successfully Deleted!!!");
        return $this->redirect()->toRoute("jobHistory");
    }
	
}

/* End of file JobHistoryController.php */
/* Location: ./Setup/src/Controller/JobHistoryController.php */