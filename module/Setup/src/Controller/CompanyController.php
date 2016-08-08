<?php

namespace Setup\Controller;

use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Model\Company;
use Zend\View\View;
use Setup\Model\CompanyRepository;

class CompanyController extends AbstractActionController{
	private $repository;
    private $company;
    private $form;

	function __construct(AdapterInterface $adapter)
	{
		$this->repository = new CompanyRepository($adapter);
	}

	 public function initializeForm()
    {
        $this->company = new Company();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($this->company);
        }
    }


	public function indexAction(){
		$companyList = $this->repository->fetchAll();
        $request = $this->getRequest();

        return Helper::addFlashMessagesToArray($this,['companyList' => $companyList]);

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
            $this->company->exchangeArray($this->form->getData());
            $this->repository->add($this->company);
            
            $this->flashmessenger()->addMessage("Company Successfully added!!!");
            return $this->redirect()->toRoute("company");
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
		
		$id=(int) $this->params()->fromRoute("id");
		if($id===0){
			return $this->redirect()->toRoute('company');
		}
        $this->initializeForm();

        $request=$this->getRequest();

        if(!$request->isPost()){
            $this->form->bind($this->repository->fetchById($id));
            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->form,'id'=>$id]
                );
        }

        $modifiedDt = date("Y-m-d");

        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
            $this->company->exchangeArray($this->form->getData());
            $this->repository->edit($this->company,$id,$modifiedDt);
            $this->flashmessenger()->addMessage("Company Successfully Updated!!!");
           return $this->redirect()->toRoute("company");
        } else {
            return Helper::addFlashMessagesToArray(
                $this,['form'=>$this->form,'id'=>$id]
             );

        }
	}
	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
		$this->repository->delete($id);
        $this->flashmessenger()->addMessage("Company Successfully Deleted!!!");
		return $this->redirect()->toRoute('company');
	}
}
	



?>