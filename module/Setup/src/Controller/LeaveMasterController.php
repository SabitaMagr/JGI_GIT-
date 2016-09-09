<?php

namespace Setup\Controller;

use Application\Helper\Helper;
use Setup\Form\LeaveMasterForm;
use Setup\Model\LeaveMaster;
use Setup\Repository\LeaveMasterRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LeaveMasterController extends AbstractActionController
{

    private $repository;
    private $form;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new LeaveMasterRepository($adapter);
        $this->adapter = $adapter;
    }

    public function indexAction()
    {
        $leaveList = $this->repository->fetchAll();
        $getValue=function(){
            $kv=['Y'=>'Yes','N'=>'No'];
            return function($key) use ($kv){
                return $kv[$key];
            };
        };
        return Helper::addFlashMessagesToArray($this, ['leaveList' => $leaveList,'getValue'=>$getValue()]);
    }

    public function initializeForm()
    {
        $leaveMasterForm = new LeaveMasterForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveMasterForm);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveMaster = new LeaveMaster();
                $leaveMaster->exchangeArrayFromForm($this->form->getData());
                $leaveMaster->leaveId = ((int)Helper::getMaxId($this->adapter, "HR_LEAVE_MASTER_SETUP", "LEAVE_ID")) + 1;
                $leaveMaster->createdDt = Helper::getcurrentExpressionDate();

                $leaveMaster->status = 'E';
                $this->repository->add($leaveMaster);
                $this->flashmessenger()->addMessage("Leave Successfully added!!!");
                return $this->redirect()->toRoute("leave");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'customRenderer' => Helper::renderCustomView()
            ]
        )
        );
    }

    public function editAction()
    {
        $this->initializeForm();
        $id = (int)$this->params()->fromRoute("id");

        if ($id === 0) {
            return $this->redirect()->toRoute("shift");
        }

        $request = $this->getRequest();
        $leaveMaster = new LeaveMaster();
        if (!$request->isPost()) {
            $leaveMaster->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($leaveMaster);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveMaster->exchangeArrayFromForm($this->form->getData());
                $leaveMaster->modifiedDt = Helper::getcurrentExpressionDate();

                $this->repository->edit($leaveMaster, $id);
                $this->flashmessenger()->addMessage("Leave Successfuly Updated!!!");
                return $this->redirect()->toRoute("leave");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'id' => $id,
                'customRenderer' => Helper::renderCustomView()
            ]
        )
        );
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('leave');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Leave Successfully Deleted!!!");
        return $this->redirect()->toRoute('leave');
    }
}
