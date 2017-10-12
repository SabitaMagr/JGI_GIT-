<?php

namespace SelfService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Exception;
use SelfService\Repository\AttendanceRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class MyAttendance extends AbstractActionController {
    private $adapter;
    private $repository;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter=$adapter;
        $this->repository = new AttendanceRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function indexAction() {
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $status = array(
            "All" => "All Status",
            "P" => "Present Only",
            "A" => "Absent Only",
            "H" => "On Holiday",
            "L" => "On Leave",
            "T" => "On Training",
            "TVL" => "On Travel",
            "WOH" => "Work on Holiday",
            "WOD" => "Work on DAYOFF",
            "LI" => "Late In",
            "EO" => "Early Out"
        );
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "statusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");

        $attendanceList = $this->repository->fetchByEmpId($this->employeeId);
        $fiscal_year = $this->repository->getCurrentNeplaiMonthStartDateEndDate();
        return Helper::addFlashMessagesToArray($this, [
                    'attendanceList' => $attendanceList,
                    'employeeId' => $this->employeeId,
                    'status' => $statusFormElement,
                    'fiscalYear' => $fiscal_year
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
                $missPunchOnly = ((int) $filtersDetail['missPunchOnly'] == 1) ? true : false;

                $result = $attendanceRepository->attendanceReport($fromDate, $toDate, $employeeId, $status, $missPunchOnly);
                $temArray = Helper::extractDbData($result);

                return new CustomViewModel(['success' => true, 'data' => $temArray, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }

}
