<?php

namespace AttendanceManagement\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Model\ShiftSetup;
use AttendanceManagement\Repository\ShiftAssignRepository;
use Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ShiftAssign extends AbstractActionController {

    private $repository;
    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new ShiftAssignRepository($adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $shifts = EntityHelper::getTableList($this->adapter, ShiftSetup::TABLE_NAME, [ShiftSetup::SHIFT_ID, ShiftSetup::SHIFT_ENAME], [ShiftSetup::STATUS => 'E']);
        return new ViewModel([
            'shiftList' => $shifts,
            'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function addAction() {
        $shifts = EntityHelper::getTableList($this->adapter, ShiftSetup::TABLE_NAME, [ShiftSetup::SHIFT_ID, ShiftSetup::SHIFT_ENAME], [ShiftSetup::STATUS => 'E']);
        return new ViewModel([
            'shiftList' => $shifts,
            'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function listWSAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $data = $request->getPost();
            $list = $this->repository->fetchShiftAssignWithDetail($data);
            return new CustomViewModel(['success' => true, 'data' => $list, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function employeeListWSAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $data = $request->getPost();
            $list = $this->repository->fetchEmployeeList($data);
            return new CustomViewModel(['success' => true, 'data' => $list, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function addWsAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $data = $request->getPost();
            $ids = $data['employeeIds'];
            $shiftId = $data['shiftId'];
            $fromDate = Helper::getExpressionDate($data['fromDate']);
            $toDate = Helper::getExpressionDate($data['toDate']);
            foreach ($ids as $id) {
                $this->repository->bulkAdd($id, $shiftId, $fromDate->getExpression(), $toDate->getExpression(), $this->employeeId);
            }

            return new CustomViewModel(['success' => true, 'data' => null, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function editWsAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $data = $request->getPost();
            $ids = $data['employeeIds'];
            $shiftId = $data['shiftId'];
            $fromDate = Helper::getExpressionDate($data['fromDate']);
            $toDate = Helper::getExpressionDate($data['toDate']);

            foreach ($ids as $id) {
                $this->repository->bulkEdit($id, $shiftId, $fromDate->getExpression(), $toDate->getExpression(), $this->employeeId);
            }

            return new CustomViewModel(['success' => true, 'data' => null, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function employeeShiftsWSAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $data = $request->getPost();
            $employeeShifts = $this->repository->fetchEmployeeShifts($data['employeeId']);
            return new CustomViewModel(['success' => true, 'data' => $employeeShifts, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
