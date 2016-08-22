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
use Setup\Helper\EntityHelper;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\EmpCurrentPostingRepository;


class EmpCurrentPostingController extends AbstractActionController{

	private $empCurrentPosting;
	private $form;
	private $adapter;
	private $repository;

	public function __construct(AdapterInterface $adapter){
		$this->adapter = $adapter;
		$this->repository = new EmpCurrentPostingRepository($adapter);
	}

	public function initializeForm(){
		$this->empCurrentPosting = new EmpCurrentPostingForm();
		$builder = new AnnotationBuilder();
		if(!$this->form){
			$this->form = $builder->createForm($this->empCurrentPosting);
		}
	}

	public function indexAction(){
		$empCurrentPostingList = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this,['empCurrentPostingList' => $empCurrentPostingList]);
	}

	public function addAction(){
		$this->initializeForm();
		$request = $this->getRequest();

		if($request->isPost()){
		
			$this->form->setData($request->getPost());
			if($this->form->isValid()){
				$this->empCurrentPosting->exchangeArrayFromForm($this->form->getData());
				$this->repository->add($this->empCurrentPosting);			

				$this->flashmessenger()->addMessage("Employee Current Posting Successfully added!!!");
	            return $this->redirect()->toRoute("empCurrentPosting");
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
           $this->empCurrentPosting->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
           $this->form->bind((object)$this->empCurrentPosting->getArrayCopyForForm());
        }else{

            $this->empCurrentPostingForm->setData($request->getPost());
           
            if ($this->empCurrentPostingForm->isValid()) {
                $this->empCurrentPosting->exchangeArrayFromForm($this->form->getData());
                $this->repository->edit($this->empCurrentPosting,$id);

                $this->flashmessenger()->addMessage("Employee Current Posting Updated!!!");
                return $this->redirect()->toRoute("empCurrentPosting");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,
            [
            	'id'=>$id,
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

	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('empCurrentPosting');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Employee Current Posting Deleted!!!");
        return $this->redirect()->toRoute("empCurrentPosting");
	}
}

/* End of file EmpCurrentPostingController.php */
/* Location: ./Setup/src/Controller/EmpCurrentPostingController.php */