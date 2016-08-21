<?php
namespace Setup\Controller;
/**
* Master Setup for Position
* Position controller.
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
use Setup\Form\PositionForm;
use Setup\Model\PositionRepository;
use Zend\Db\Adapter\AdapterInterface;

class PositionController extends AbstractActionController
{

    private $position;
    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new PositionRepository($adapter);
    }

     public function initializeForm()
    {   
        $this->position = new PositionForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($this->position);
        }
    }

    public function indexAction()
    {
        $positionList  = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this,['positions' => $positionList]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            
            $this->form->setData($request->getPost());      
            
            if ($this->form->isValid()) {    
            
                $this->position->exchangeArrayFromForm($this->form->getData());
                $this->repository->add($this->position);
                
                $this->flashmessenger()->addMessage("Position Successfully added!!!");
                return $this->redirect()->toRoute("position");
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

    public function editAction()
    {
        $id=(int) $this->params()->fromRoute("id");
        if($id===0){
            return $this->redirect()->toRoute('position');
        }
        $this->initializeForm();
        $request=$this->getRequest();

        $modifiedDt = date("d-M-y");
        if(!$request->isPost()){
            
            $this->position->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind((object)$this->position->getArrayCopyForForm());
        }else{

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $this->position->exchangeArrayFromForm($this->form->getData());
                $this->repository->edit($this->position,$id,$modifiedDt);
                $this->flashmessenger()->addMessage("Position Successfully Updated!!!");
                return $this->redirect()->toRoute("position");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,['form'=>$this->form,'id'=>$id]
         );       
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Position Successfully Deleted!!!");
        return $this->redirect()->toRoute('position');
    }    
}

/* End of file PositionController.php */
/* Location: ./Setup/src/Controller/PositionController.php */
?>