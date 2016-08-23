<?php

namespace Setup\Controller;

/**
 * Master Setup for Shift
 * Shift controller.
 * Created By: Somkala Pachhai
 * Edited By: Somkala Pachhai
 * Date: August 5, 2016, Friday
 * Last Modified By: Somkala Pachhai
 * Last Modified Date: August 10,2016, Wednesday
 */

use Application\Helper\Helper;
use Setup\Form\ShiftForm;
use Setup\Model\Shift;
use Setup\Repository\ShiftRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ShiftController extends AbstractActionController
{

    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new ShiftRepository($adapter);
    }

    public function indexAction()
    {
        $shiftList = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['shiftList' => $shiftList]);
    }

    public function initializeForm()
    {
        $shiftFrom = new ShiftForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($shiftFrom);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $shift = new Shift();
                $shift->exchangeArrayFromForm($this->form->getData());
                $shift->createdDt = date('d-M-y');

                $this->repository->add($shift);
                $this->flashmessenger()->addMessage("Shift Successfully added!!!");
                return $this->redirect()->toRoute("shift");
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
        $this->initializeForm();
        $id = (int)$this->params()->fromRoute("id");

        if ($id === 0) {
            return $this->redirect()->toRoute("shift");
        }

        $request = $this->getRequest();
        $shift = new Shift();
        if (!$request->isPost()) {
            $shift->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($shift);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $shift->exchangeArrayFromForm($this->form->getData());
                $shift->modifiedDt = date('d-M-y');
                $this->repository->edit($shift, $id);
                $this->flashmessenger()->addMessage("Shift Successfuly Updated!!!");
                return $this->redirect()->toRoute("shift");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'id' => $id
            ]
        )
        );
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Shift Successfully Deleted!!!");
        return $this->redirect()->toRoute('shift');
    }
}


/* End of file ShiftController.php */
/* Location: ./Setup/src/Controller/ShiftController.php */