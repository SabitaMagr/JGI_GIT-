<?php

namespace HolidayManagement\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use HolidayManagement\Model\EmployeeHoliday;
use HolidayManagement\Model\Holiday;
use HolidayManagement\Repository\HolidayAssignRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class HolidayAssign extends AbstractActionController {

    private $adapter;
    private $storage;
    private $employeeId;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->storage = $auth->getStorage()->read();
        $this->employeeId = $this->storage['employee_id'];
        $this->repository = new HolidayAssignRepository($adapter);
    }

    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'holidayList' => EntityHelper::getTableKVList($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME], [Holiday::STATUS => EntityHelper::STATUS_ENABLED], null, null, Holiday::START_DATE)
        ]);
    }

    public function getEmployeeListAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();

                $employeeId = $postedData['employeeId'];
                $branchId = $postedData['branchId'];
                $departmentId = $postedData['departmentId'];
                $designationId = $postedData['designationId'];
                $positionId = $postedData['positionId'];
                $serviceTypeId = $postedData['serviceTypeId'];
                $serviceEventTypeId = $postedData['serviceEventTypeId'];
                $companyId = $postedData['companyId'];
                $genderId = $postedData['genderId'];

                $raw = $this->repository->filterEmployees($employeeId, $branchId, $departmentId, $designationId, $positionId, $serviceTypeId, $serviceEventTypeId, $companyId, $genderId);
                $reportData = Helper::extractDbData($raw);
                return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage(), 'errorDetail' => $e->getTraceAsString()]);
        }
    }

    public function getHolidayAssignedEmployeesAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();
                $holidayId = $postedData['holidayId'];

                $raw = EntityHelper::getTableKVList($this->adapter, EmployeeHoliday::TABLE_NAME, null, [EmployeeHoliday::EMPLOYEE_ID], [EmployeeHoliday::HOLIDAY_ID => $holidayId], null, null, null, null, TRUE);
                $reportData = Helper::extractDbData($raw);
                return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function assignHolidayToEmployeesAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $postedData = $request->getPost();
                $holidayId = $postedData['holidayId'];
                if (!isset($postedData['employeeIdList'])) {
                    $employeeIdList = [];
                } else {
                    $employeeIdList = $postedData['employeeIdList'];
                }
                $reportData = $this->repository->multipleEmployeeAssignToHoliday($holidayId, $employeeIdList);
                return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
