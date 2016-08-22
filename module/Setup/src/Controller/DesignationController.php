<?php

namespace Setup\Controller;

/**
* Master Setup for Designation
* Designation controller.
* Created By: Ukesh Gaiju
* Edited By: Somkala Pachhai
* Date: August 3, 2016, Friday 
* Last Modified By: Somkala Pachhai
* Last Modified Date: August 10,2016, Wednesday 
*/

use Application\Helper\Helper;
use Setup\Form\DesignationForm;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\DesignationRepository;

class DesignationController extends AbstractActionController
{
    private $repository;
    private $desgination;
    private $form;

    function __construct(AdapterInterface $adapter)
    {
        $this->repository = new DesignationRepository($adapter);
    }

    public function initializeForm(){
        $this->designation = new DesignationForm();
        $builder = new AnnotationBuilder();
        if(!$this->form){
            $this->form = $builder->createForm($this->designation);
        }
    }

    public function indexAction()
    {
        $designations = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this,["designations" => $designations]);
    }

    public function addAction(){
        
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
          
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $this->designation->exchangeArrayFromForm($this->form->getData());
                $this->repository->add($this->designation);
                $this->flashmessenger()->addMessage("Designation Successfully added!!!");
                return $this->redirect()->toRoute("designation");
            } 
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'messages' => $this->flashmessenger()->getMessages()
             ]
            )
        );
               
    }

    public function editAction(){
        
        $id=(int) $this->params()->fromRoute("id");
        if($id===0){
            return $this->redirect()->toRoute('designation');
        }
        $this->initializeForm();
        $request=$this->getRequest();

        if(!$request->isPost()){
            $this->designation->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind((object)$this->designation->getArrayCopyForForm());
        }else{

            $this->form->setData($request->getPost());
            $modifiedDt = date('d-M-y');
            if ($this->form->isValid()) {
                
                $this->designation->exchangeArrayFromForm($this->form->getData());
                $this->repository->edit($this->designation,$id,$modifiedDt);

                $this->flashmessenger()->addMessage("Designation Successfully Updated!!!");
                return $this->redirect()->toRoute("designation");
            }
        }
        return Helper::addFlashMessagesToArray(
                    $this,['form'=>$this->form,'id'=>$id]
                 );
    }

    public function deleteAction(){
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('designation');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Designation Successfully Deleted!!!");
        return $this->redirect()->toRoute('designation');
    }
}
/* End of file DesignationController.php */
/* Location: ./Setup/src/Controller/DesignationController.php */
