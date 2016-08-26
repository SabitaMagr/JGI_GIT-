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

use Setup\Model\JobHistory;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Setup\Form\JobHistoryForm;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Repository\JobHistoryRepository;
use Setup\Helper\EntityHelper;

class JobHistoryController extends AbstractActionController{

	private $repository;
	private $form;
	private $adapter;

	public function __construct(AdapterInterface $adapter){
		$this->repository = new JobHistoryRepository($adapter);
		$this->adapter = $adapter;
	}

	public function initializeForm(){
		$jobHistoryForm = new JobHistoryForm();
		$builder = new AnnotationBuilder();
		if(!$this->form){
			$this->form = $builder->createForm($jobHistoryForm);
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
				$jobHistory=new JobHistory();
	           	$jobHistory->exchangeArrayFromForm($this->form->getData());
				$jobHistory->jobHistoryId=((int) Helper::getMaxId($this->adapter,"HR_JOB_HISTORY","JOB_HISTORY_ID"))+1;
				$jobHistory->startDate=Helper::getExpressionDate($jobHistory->startDate);
				$jobHistory->endDate=Helper::getExpressionDate($jobHistory->endDate);
	           	$this->repository->add($jobHistory);
	            $this->flashmessenger()->addMessage("Job History Successfully added!!!");
	            return $this->redirect()->toRoute("jobHistory");
	        } 
    	}
        return Helper::addFlashMessagesToArray(
			$this,
			[
				'form'=>$this->form,
				'messages' => $this->flashmessenger()->getMessages(),
				'departments'=>EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_DEPARTMENTS),
				'designations'=>EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_DESIGNATIONS),
				'branches'=>EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_BRANCHES),
				'positions'=>EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_POSITIONS),
				'serviceTypes'=>EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_SERVICE_TYPES),
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

		$jobHistory=new JobHistory();
        if(!$request->isPost()){
          $jobHistory->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
          $this->form->bind($jobHistory);
        }else{

            $this->form->setData($request->getPost());           
            if ($this->form->isValid()) {
                
                $jobHistory->exchangeArrayFromForm($this->form->getData());

				$jobHistory->startDate=Helper::getExpressionDate($jobHistory->startDate);
				$jobHistory->endDate=Helper::getExpressionDate($jobHistory->endDate);

                $this->repository->edit($jobHistory,$id);
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
				'departments'=>EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_DEPARTMENTS),
				'designations'=>EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_DESIGNATIONS),
				'branches'=>EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_BRANCHES),
				'positions'=>EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_POSITIONS),
				'serviceTypes'=>EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_SERVICE_TYPES),
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