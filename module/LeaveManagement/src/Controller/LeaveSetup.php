<?php

namespace LeaveManagement\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use LeaveManagement\Form\LeaveMasterForm;
use LeaveManagement\Model\LeaveMaster;
use LeaveManagement\Repository\LeaveMasterRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LeaveSetup extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new LeaveMasterRepository($adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $leaveList = $this->repository->fetchAll();
        $leaves = [];

        $getValue = function($kv) {
            if ($kv == 'Y') {
                return 'Yes';
            } else if ($kv == 'N') {
                return 'No';
            }
        };

        foreach ($leaveList as $leaveRow) {
            array_push($leaves, [
                'LEAVE_ID' => $leaveRow['LEAVE_ID'],
                'LEAVE_CODE' => $leaveRow['LEAVE_CODE'],
                'LEAVE_ENAME' => $leaveRow['LEAVE_ENAME'],
                'ALLOW_HALFDAY' => $getValue($leaveRow['ALLOW_HALFDAY']),
                'DEFAULT_DAYS' => $getValue($leaveRow['DEFAULT_DAYS']),
                'CARRY_FORWARD' => $getValue($leaveRow['CARRY_FORWARD']),
                'CASHABLE' => $getValue($leaveRow['CASHABLE']),
                'PAID'=>$getValue($leaveRow['PAID']),
                'IS_SUBSTITUTE' => $leaveRow['IS_SUBSTITUTE']
                    ]
            );
        }
        return Helper::addFlashMessagesToArray($this, ['leaves' => $leaves]);
    }

    public function initializeForm() {
        $leaveMasterForm = new LeaveMasterForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveMasterForm);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $leaveMaster = new LeaveMaster();
                $leaveMaster->exchangeArrayFromForm($this->form->getData());
                $leaveMaster->leaveId = ((int) Helper::getMaxId($this->adapter, LeaveMaster::TABLE_NAME, LeaveMaster::LEAVE_ID)) + 1;
                $leaveMaster->createdDt = Helper::getcurrentExpressionDate();
                $leaveMaster->createdBy = $this->employeeId;

                $leaveMaster->status = 'E';
                $this->repository->add($leaveMaster);
                $this->flashmessenger()->addMessage("Leave Successfully added!!!");
                return $this->redirect()->toRoute("leavesetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
                    'fiscalYears' => EntityHelper::getTableKVList($this->adapter, "HRIS_FISCAL_YEARS", "FISCAL_YEAR_ID", ["START_DATE", "END_DATE"], null, "|")
                        ]
                )
        );
    }

    public function editAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("id");

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
                $leaveMaster->modifiedBy = $this->employeeId;

                $this->repository->edit($leaveMaster, $id);
                $this->flashmessenger()->addMessage("Leave Successfuly Updated!!!");
                return $this->redirect()->toRoute("leavesetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'customRenderer' => Helper::renderCustomView(),
                    'fiscalYears' => EntityHelper::getTableKVList($this->adapter, "HRIS_FISCAL_YEARS", "FISCAL_YEAR_ID", ["START_DATE", "END_DATE"], null, "|")
                        ]
                )
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('leavesetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Leave Successfully Deleted!!!");
        return $this->redirect()->toRoute('leavesetup');
    }

}
