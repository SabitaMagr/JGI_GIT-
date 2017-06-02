<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AttendanceManagement\Controller;

use Application\Helper\Helper;
use AttendanceManagement\Form\ShiftAdjustmentForm;
use AttendanceManagement\Model\ShiftAdjustmentModel;
use AttendanceManagement\Repository\ShiftAdjustmentRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Description of ShiftAdjustment
 *
 * @author root
 */
class ShiftAdjustment extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new ShiftAdjustmentRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $shiftAdjustmentForm = new ShiftAdjustmentForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($shiftAdjustmentForm);
    }

    public function indexAction() {
        $shiftAdjustmentList = $this->repository->fetchAll();
        $shiftsAdj = [];
        foreach ($shiftAdjustmentList as $shiftRow) {
            array_push($shiftsAdj, $shiftRow);
        }
        return Helper::addFlashMessagesToArray($this, ['shiftsAdjust' => $shiftsAdj]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
//            echo '<pre>';
//            print_r($request->getPost());
//            die();
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $shiftAdjust = new ShiftAdjustmentModel();
                $shiftAdjust->exchangeArrayFromForm($this->form->getData());
                $shiftAdjust->adjustmentId = ((int) Helper::getMaxId($this->adapter, ShiftAdjustmentModel::TABLE_NAME, ShiftAdjustmentModel::ADJUSTMENT_ID)) + 1;
                $shiftAdjust->startTime = Helper::getExpressionTime($shiftAdjust->startTime);
                $shiftAdjust->endTime = Helper::getExpressionTime($shiftAdjust->endTime);
                $shiftAdjust->createdBy = $this->employeeId;
                $shiftAdjust->createdDt = Helper::getcurrentExpressionDate();
                $this->repository->add($shiftAdjust);
                $this->flashmessenger()->addMessage("ShiftAdjustment Successfully added!!!");
                return $this->redirect()->toRoute("shiftAdjustment");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form
                        ]
        );
    }

    public function editAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("id");

        if ($id === 0) {
            return $this->redirect()->toRoute("shiftAdjustment");
        }

        $request = $this->getRequest();
        $shiftAdjust = new ShiftAdjustmentModel();

        if (!$request->isPost()) {
            $shiftAdjust->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($shiftAdjust);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                
                $shiftAdjust->exchangeArrayFromForm($this->form->getData());
//                $shiftAdjust->adjustmentId = ((int) Helper::getMaxId($this->adapter, ShiftAdjustmentModel::TABLE_NAME, ShiftAdjustmentModel::ADJUSTMENT_ID)) + 1;
                $shiftAdjust->startTime = Helper::getExpressionTime($shiftAdjust->startTime);
                $shiftAdjust->endTime = Helper::getExpressionTime($shiftAdjust->endTime);
                $shiftAdjust->modifiedBy = $this->employeeId;
                $shiftAdjust->modifiedDt = Helper::getcurrentExpressionDate();              


                $this->repository->edit($shiftAdjust, $id);
                $this->flashmessenger()->addMessage("shiftAdjustment Successfuly Updated!!!");
                return $this->redirect()->toRoute("shiftAdjustment");
            }
        }


        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                        ]
        );
    }

}
