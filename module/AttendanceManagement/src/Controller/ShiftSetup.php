<?php

namespace AttendanceManagement\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper as EntityHelper2;
use Application\Helper\Helper;
use AttendanceManagement\Form\ShiftForm;
use AttendanceManagement\Model\ShiftSetup as Shift;
use AttendanceManagement\Repository\ShiftRepository;
use Exception;
use Setup\Model\Company;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ShiftSetup extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->repository = new ShiftRepository($adapter);
        $this->adapter = $adapter;
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $shiftList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $shiftList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function initializeForm() {
        $shiftFrom = new ShiftForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($shiftFrom);
    }

    public function addAction() {
        ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this);
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
                $shift->graceStartTime = Helper::getExpressionTime($shift->graceStartTime);
                $shift->graceEndTime = Helper::getExpressionTime($shift->graceEndTime);
                $shift->halfDayInTime = Helper::getExpressionTime($shift->halfDayInTime);
                $shift->halfDayOutTime = Helper::getExpressionTime($shift->halfDayOutTime);

                $shift->actualWorkingHr = Helper::hoursToMinutes($shift->actualWorkingHr);
                $shift->totalWorkingHr = Helper::hoursToMinutes($shift->totalWorkingHr);
                $shift->lateIn = Helper::hoursToMinutes($shift->lateIn);
                $shift->earlyOut = Helper::hoursToMinutes($shift->earlyOut);



                $this->repository->add($shift);
                $this->flashmessenger()->addMessage("Shift Successfully added!!!");
                return $this->redirect()->toRoute("shiftsetup");
            }
        }

        $defaultShift = $this->repository->DefaultShift();

        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
                    'companies' => EntityHelper2::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], [Company::STATUS => 'E'], "COMPANY_NAME", "ASC", NULL, TRUE, TRUE),
                    'anotherDefaultShift' => $defaultShift
                        ]
                )
        );
    }

    public function editAction() {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("id");

        if ($id === 0) {
            return $this->redirect()->toRoute("shiftsetup");
        }

        $request = $this->getRequest();
        $shift = new Shift();
        if (!$request->isPost()) {
            $shift->exchangeArrayFromDB($this->repository->fetchById($id));
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

                $shift->graceStartTime = Helper::getExpressionTime($shift->graceStartTime);
                $shift->graceEndTime = Helper::getExpressionTime($shift->graceEndTime);

                $shift->actualWorkingHr = Helper::hoursToMinutes($shift->actualWorkingHr);
                $shift->totalWorkingHr = Helper::hoursToMinutes($shift->totalWorkingHr);
                $shift->lateIn = Helper::hoursToMinutes($shift->lateIn);
                $shift->earlyOut = Helper::hoursToMinutes($shift->earlyOut);


                $this->repository->edit($shift, $id);
                $this->flashmessenger()->addMessage("Shift Successfuly Updated!!!");
                return $this->redirect()->toRoute("shiftsetup");
            }
        }
        $defaultShift = $this->repository->DefaultShift();
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'customRenderer' => Helper::renderCustomView(),
                    'companies' => EntityHelper2::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], [Company::STATUS => 'E'], "COMPANY_NAME", "ASC", NULL, TRUE, TRUE),
                    'anotherDefaultShift' => $defaultShift
                        ]
                )
        );
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this)) {
            return;
        };
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
