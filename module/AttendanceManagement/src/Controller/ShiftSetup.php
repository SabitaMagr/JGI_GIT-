<?php

namespace AttendanceManagement\Controller;

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
use AttendanceManagement\Form\ShiftForm;
use AttendanceManagement\Model\ShiftSetup as Shift;
use AttendanceManagement\Repository\ShiftRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZF\DevelopmentMode\Help;

class ShiftSetup extends AbstractActionController
{

    private $repository;
    private $form;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new ShiftRepository($adapter);
        $this->adapter=$adapter;
    }

    public function indexAction()
    {
        $shiftList = $this->repository->fetchAll();
        $shifts = [];
        foreach($shiftList as $shiftRow){
            array_push($shifts, $shiftRow);
        }
        return Helper::addFlashMessagesToArray($this, ['shifts' => $shifts]);
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
                $shift->shiftId=((int) Helper::getMaxId($this->adapter,Shift::TABLE_NAME,Shift::SHIFT_ID))+1;
                $shift->startDate=Helper::getExpressionDate($shift->startDate);
                $shift->endDate=Helper::getExpressionDate($shift->endDate);
                $shift->startTime=Helper::getExpressionTime($shift->startTime);
                $shift->endTime=Helper::getExpressionTime($shift->endTime);
                $shift->halfDayEndTime=Helper::getExpressionTime($shift->halfDayEndTime);
                $shift->halfTime=Helper::getExpressionTime($shift->halfTime);
                $shift->createdDt = Helper::getcurrentExpressionDate();

//                $shift->shiftLname=mb_convert_encoding($shift->shiftLname, 'UTF-16LE');
//                print "<pre>";
//                print_r($shift->shiftLname);
//                exit;

                $shift->status='E';
                $this->repository->add($shift);
                $this->flashmessenger()->addMessage("Shift Successfully added!!!");
                return $this->redirect()->toRoute("shiftsetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'customRenderer'=>Helper::renderCustomView()
            ]
        )
        );
    }

    public function editAction()
    {
        $this->initializeForm();
        $id = (int)$this->params()->fromRoute("id");

        if ($id === 0) {
            return $this->redirect()->toRoute("shiftsetup");
        }

        $request = $this->getRequest();
        $shift = new Shift();
        if (!$request->isPost()) {
            $shift->exchangeArrayFromDB($this->repository->fetchById($id));
//                $shift->shiftLname=mb_convert_encoding($shift->shiftLname, 'UTF-8','UTF-16LE' );
            $this->form->bind($shift);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $shift->exchangeArrayFromForm($this->form->getData());
                $shift->shiftId=((int) Helper::getMaxId($this->adapter,Shift::TABLE_NAME,Shift::SHIFT_ID))+1;
                $shift->startDate=Helper::getExpressionDate($shift->startDate);
                $shift->endDate=Helper::getExpressionDate($shift->endDate);
                $shift->startTime=Helper::getExpressionTime($shift->startTime);
                $shift->endTime=Helper::getExpressionTime($shift->endTime);
                $shift->halfDayEndTime=Helper::getExpressionTime($shift->halfDayEndTime);
                $shift->halfTime=Helper::getExpressionTime($shift->halfTime);
                $shift->modifiedDt = Helper::getcurrentExpressionDate();

                $this->repository->edit($shift, $id);
                $this->flashmessenger()->addMessage("Shift Successfuly Updated!!!");
                return $this->redirect()->toRoute("shiftsetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'id' => $id,
                'customRenderer'=>Helper::renderCustomView()
            ]
        )
        );
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('shiftsetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Shift Successfully Deleted!!!");
        return $this->redirect()->toRoute('shiftsetup');
    }
}


/* End of file ShiftController.php */
/* Location: ./Setup/src/Controller/ShiftController.php */
