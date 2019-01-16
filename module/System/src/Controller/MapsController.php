<?php
namespace System\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use System\Repository\MapsRepository;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\View\Model\JsonModel;

class MapsController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(MapsRepository::class);
    }

    public function showMapAction(){
        $request = $this->getRequest();
        $data = $request->getPost();
        $employeeId = $data['employee'];

        if($employeeId == -1){
            $this->flashmessenger()->addMessage("Please select an employee.");
            return $this->redirect()->toRoute("showlocation");
        }

        $attd_from_date = $data['fromDate'];
        $attd_to_date = $data['toDate'];

        if($attd_from_date == null){
            $this->flashmessenger()->addMessage("Please select from date.");
            return $this->redirect()->toRoute("showlocation");
        }

        if(empty($attd_to_date)){
            $attd_to_date = $attd_from_date;
        }

        $begin = new \DateTime($attd_from_date);
        $end = new \DateTime($attd_to_date);
        $end->modify('+1 day');

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);

        $checkInLocation = array();
        $checkOutLocation = array();
        $checkInTime = array();
        $checkOutTime = array();

        foreach ($period as $dt) {
            $checkInLoc = $this->repository->fetchCheckInLocation($employeeId, $dt->format("d-M-y"));
            $locData1 = $checkInLoc->current();
            if(isset($locData1) && $locData1 != null && $locData1 != 'null' && $locData1 != false && !empty($locData1)){
                $locData1 = json_encode($locData1);
                $location1 = json_decode($locData1)->LOCATION;
                $time1 = json_decode($locData1)->ATTENDANCE_TIME;
                array_push($checkInLocation, $location1);
                array_push($checkInTime, $time1);
            }

            $checkOutLoc = $this->repository->fetchCheckOutLocation($employeeId, $dt->format("d-M-y"));
            $locData2 = $checkOutLoc->current();
            if(isset($locData2) && $locData2 != null && $locData2 != 'null' && $locData2 != false && !empty($locData2)){
                $locData2 = json_encode($locData2);
                $location2 = json_decode($locData2)->LOCATION;
                $time2 = json_decode($locData2)->ATTENDANCE_TIME;
                array_push($checkOutLocation, $location2);
                array_push($checkOutTime, $time2);
            } 
        }
        
        $employeeName = $this->repository->getEmployeeData($employeeId);
        $employee = $employeeName->current();
        $employee = json_encode($employee);

        return Helper::addFlashMessagesToArray($this, [
            'status' => $this->getStatusSelect(),
            'presentStatus' => $this->getPresentStatusSelect(),
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'acl' => $this->acl,
            'locData1' => $checkInLocation, 
            'locData2' => $checkOutLocation,
            'timeData1' => $checkInTime, 
            'timeData2' => $checkOutTime,
            'employee' => $employee,
            'empcodepref' => $this->getEmployeeCodePreference(),
            'employeeDetail' => $this->storageData['employee_detail']
        ]);
    }

    private function getStatusSelect() {
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $status = array(
            "P" => "Present Only",
            "A" => "Absent Only",
            "H" => "On Holiday",
            "L" => "On Leave",
            "T" => "On Training",
            "TVL" => "On Travel",
            "WOH" => "Work on Holiday",
            "WOD" => "Work on DAYOFF",
        );
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "statusId", "class" => "form-control", "multiple" => "multiple"]);
        $statusFormElement->setLabel("Status");
        return $statusFormElement;
    }

    private function getPresentStatusSelect() {
        $statusFormElement = new Select();
        $statusFormElement->setName("presentStatus");
        $status = array(
            "LI" => "Late In",
            "EO" => "Early Out",
            "MP" => "Missed Punched",
        );
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "presentStatusId", "class" => "form-control", "multiple" => "multiple"]);
        $statusFormElement->setLabel("Present Status");
        return $statusFormElement;
    }

    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, [
                'status' => $this->getStatusSelect(),
                'presentStatus' => $this->getPresentStatusSelect(),
                'searchValues' => EntityHelper::getSearchData($this->adapter),
                'acl' => $this->acl,
                'empcodepref' => $this->getEmployeeCodePreference(),
                'employeeDetail' => $this->storageData['employee_detail']
        ]);
    }
}
