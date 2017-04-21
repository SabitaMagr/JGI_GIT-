<?php

namespace AttendanceManagement\Controller;

use Application\Helper\Helper;
use AttendanceManagement\Form\ShiftForm;
use AttendanceManagement\Model\ShiftSetup as Shift;
use AttendanceManagement\Repository\ShiftRepository;
use Setup\Helper\EntityHelper;
use Application\Helper\EntityHelper as EntityHelper2;
use Setup\Model\Company;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ShiftSetup extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new ShiftRepository($adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $shiftList = $this->repository->fetchAll();
        $shifts = [];
        foreach ($shiftList as $shiftRow) {
            array_push($shifts, $shiftRow);
        }
        return Helper::addFlashMessagesToArray($this, ['shifts' => $shifts]);
    }

    public function initializeForm() {
        $shiftFrom = new ShiftForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($shiftFrom);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $shift = new Shift();
                $shift->exchangeArrayFromForm($this->form->getData());
                $shift->shiftId = ((int) Helper::getMaxId($this->adapter, Shift::TABLE_NAME, Shift::SHIFT_ID)) + 1;
                $shift->startDate = Helper::getExpressionDate($shift->startDate);
                $shift->endDate = Helper::getExpressionDate($shift->endDate);
                $shift->startTime = Helper::getExpressionTime($shift->startTime);
                $shift->endTime = Helper::getExpressionTime($shift->endTime);
                $shift->halfDayEndTime = Helper::getExpressionTime($shift->halfDayEndTime);
                $shift->halfTime = Helper::getExpressionTime($shift->halfTime);
                $shift->createdDt = Helper::getcurrentExpressionDate();
                $shift->createdBy = $this->employeeId;
                $shift->status = 'E';

                $shift->actualWorkingHr = Helper::getExpressionTime($shift->actualWorkingHr, Helper::ORACLE_TIMESTAMP_FORMAT);
                $shift->totalWorkingHr = Helper::getExpressionTime($shift->totalWorkingHr, Helper::ORACLE_TIMESTAMP_FORMAT);
                $shift->lateIn = Helper::getExpressionTime($shift->lateIn, Helper::ORACLE_TIMESTAMP_FORMAT);
                $shift->earlyOut = Helper::getExpressionTime($shift->earlyOut, Helper::ORACLE_TIMESTAMP_FORMAT);


//                $shift->shiftLname=mb_convert_encoding($shift->shiftLname, 'UTF-16LE');
//                print "<pre>";
//                print_r($shift->shiftLname);
//                exit;

                $this->repository->add($shift);
                $this->flashmessenger()->addMessage("Shift Successfully added!!!");
                return $this->redirect()->toRoute("shiftsetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
                    'companies' => EntityHelper2::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], [Company::STATUS => 'E'], "COMPANY_NAME", "ASC",NULL,FALSE,TRUE)
                        ]
                )
        );
    }

    public function editAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("id");

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
                $shift->shiftId = ((int) Helper::getMaxId($this->adapter, Shift::TABLE_NAME, Shift::SHIFT_ID)) + 1;
                $shift->startDate = Helper::getExpressionDate($shift->startDate);
                $shift->endDate = Helper::getExpressionDate($shift->endDate);
                $shift->startTime = Helper::getExpressionTime($shift->startTime);
                $shift->endTime = Helper::getExpressionTime($shift->endTime);
                $shift->halfDayEndTime = Helper::getExpressionTime($shift->halfDayEndTime);
                $shift->halfTime = Helper::getExpressionTime($shift->halfTime);
                $shift->modifiedDt = Helper::getcurrentExpressionDate();
                $shift->modifiedBy = $this->employeeId;

                $shift->actualWorkingHr = Helper::getExpressionTime($shift->actualWorkingHr, Helper::ORACLE_TIMESTAMP_FORMAT);
                $shift->totalWorkingHr = Helper::getExpressionTime($shift->totalWorkingHr, Helper::ORACLE_TIMESTAMP_FORMAT);
                $shift->lateIn = Helper::getExpressionTime($shift->lateIn, Helper::ORACLE_TIMESTAMP_FORMAT);
                $shift->earlyOut = Helper::getExpressionTime($shift->earlyOut, Helper::ORACLE_TIMESTAMP_FORMAT);



                $this->repository->edit($shift, $id);
                $this->flashmessenger()->addMessage("Shift Successfuly Updated!!!");
                return $this->redirect()->toRoute("shiftsetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'customRenderer' => Helper::renderCustomView(),
                    'companies' => EntityHelper2::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], [Company::STATUS => 'E'], "COMPANY_NAME", "ASC",NULL,FALSE,TRUE)
                        ]
                )
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
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
