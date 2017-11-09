<?php

namespace SelfService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use SelfService\Repository\AttendanceRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class MyAttendance extends AbstractActionController {

    private $adapter;
    private $repository;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new AttendanceRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function indexAction() {
        $statusSelectElement = EntityHelper::getAttendanceStatusSelectElement();
        $presentStatusSelectElement = EntityHelper::getAttendancePresentStatusSelectElement();
        return Helper::addFlashMessagesToArray($this, [
                    'employeeId' => $this->employeeId,
                    'status' => $statusSelectElement,
                    'presentStatus' => $presentStatusSelectElement,
                    'fiscalYear' => $this->storageData['fiscal_year']
        ]);
    }

    public function pullAttendanceListAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $postedData = $request->getPost();
                $attendanceRepository = new AttendanceRepository($this->adapter);
                $filtersDetail = $postedData->data;
                $employeeId = $filtersDetail['employeeId'];
                $fromDate = $filtersDetail['fromDate'];
                $toDate = $filtersDetail['toDate'];
                $status = $filtersDetail['status'];
                $presentStatus = $filtersDetail['presentStatus'];

                $result = $attendanceRepository->attendanceReport($employeeId, $fromDate, $toDate, $status, $presentStatus);
                $itemList = Helper::extractDbData($result);

                return new CustomViewModel(['success' => true, 'data' => $itemList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }

}
