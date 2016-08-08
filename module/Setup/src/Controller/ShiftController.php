<?php 

namespace Setup\Controller;

use Application\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\Shift;
use Setup\Model\ShiftRepository;
use Zend\View\View;


class ShiftController extends AbstractActionController {
	private $shift;
	private $form;
	private $repository;

	public function __construct(AdapterInterface $adapter){
		$this->repository =  new ShiftRepository($adapter);
	}

	public function indexAction(){
		$shiftList = $this->repository->fetchAll();
		return Helper::addFlashMessagesToArray($this,['shiftList'=>$shiftList]);
	}

	public function initializeForm(){
		$this->shift = new Shift();
		$builder = new AnnotationBuilder();
		$this->form = $builder->createForm($this->shift);
	}

	public function addAction(){
		$this->initializeForm();
        $request = $this->getRequest();
        if (!$request->isPost()) {
           return new ViewModel(Helper::addFlashMessagesToArray(
                $this,
                [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages()
                 ]
                )
            );
        }

        $this->form->setData($request->getPost());
        if ($this->form->isValid()) {
            $this->shift->exchangeArray($this->form->getData());
            $this->repository->add($this->shift);
            
            $this->flashmessenger()->addMessage("Shift Successfully added!!!");
            return $this->redirect()->toRoute("shift");
        } else {
            return new ViewModel(Helper::addFlashMessagesToArray(
                $this,
                [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages()
                 ]
                )
            );
        }

	}

	public function editAction(){
		$this->initializeForm();
		$id = (int) $this->params()->fromRoute("id");

		if($id===0){
			return $this->redirect()->toRoute("shift");
		}

		$request = $this->getRequest();

		if(!$request->isPost()){
			$this->form->bind($this->repository->fetchById($id));
			return Helper::addFlashMessagesToArray($this,['form'=>$this->form,'id'=>$id]);
		}

		$this->form->setData($request->getPost());

		if($this->form->isValid()){
			$this->shift->exchangeArray($this->form->getData());

			$this->repository->edit($this->shift,$id);
			
			$this->flashmessenger()->addMessage("Shift Successfuly Updated!!!");
			return $this->redirect()->toRoute("shift");
		}else{
			return new ViewModel(Helper::addFlashMessagesToArray(
                $this,
                [
                    'form' => $this->form,
                    'id'=>$id
                 ]
                )
            );
		}


	}

	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Shift Successfully Deleted!!!");
        return $this->redirect()->toRoute('shift');
	}
}


