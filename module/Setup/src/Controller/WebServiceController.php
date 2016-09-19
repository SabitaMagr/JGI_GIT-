<?php

namespace Setup\Controller;

use Application\Helper\Helper;
use LeaveManagement\Model\LeaveAssign;
use LeaveManagement\Repository\LeaveAssignRepository;
use Setup\Helper\EntityHelper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Helper\EntityHelper as ApplicationEntityHelper;
use HolidayManagement\Repository\HolidayRepository;

class WebServiceController extends AbstractActionController
{
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $responseData = [];
        if ($request->isPost()) {
            $postedData = $request->getPost();
            switch ($postedData->action) {
                case "assignedLeaves":
                    $leaveAssignRepo = new LeaveAssignRepository($this->adapter);
                    $result = $leaveAssignRepo->fetchByEmployeeId($postedData->id);
                    $tempArray = [];
                    foreach ($result as $item) {
                        array_push($tempArray, $item);
                    }
                    $responseData = [
                        "success" => true,
                        "data" => $tempArray
                    ];
                    break;
                case "assignList":
                    $assignRepo = new LeaveAssignRepository($this->adapter);
                    $assignList = $assignRepo->fetchByEmployeeId($postedData->id);
                    $tempArray = [];
                    foreach ($assignList as $item) {
                        array_push($tempArray, $item);
                    }
                    $responseData = [
                        "success" => true,
                        "data" => $tempArray
                    ];
                    break;

                case "pullEmployeeLeave":
                    $leaveAssign = new LeaveAssignRepository($this->adapter);
                    $ids = $postedData->id;
                    $temp = $leaveAssign->filter($ids['leaveId'], $ids['branchId'], $ids['departmentId'], $ids['genderId'], $ids['designationId']);
                    $tempArray = [];
                    foreach ($temp as $item) {
                        array_push($tempArray, $item);
                    }
                    $responseData = [
                        "success" => true,
                        "data" => $tempArray
                    ];
                    break;

                case "pushEmployeeLeave":
                    $data = $postedData->data;
                    $leaveAssign = new LeaveAssign();
                    $leaveAssign->balance = $data['balance'];
                    $leaveAssign->employeeId = $data['employeeId'];
                    $leaveAssign->leaveId = $data['leave'];

                    $leaveAssignRepo = new LeaveAssignRepository($this->adapter);
                    if (empty($data['leaveId'])) {
                        $leaveAssign->createdDt = Helper::getcurrentExpressionDate();
                        $leaveAssignRepo->add($leaveAssign);
                    } else {
                        $leaveAssign->modifiedDt = Helper::getcurrentExpressionDate();
                        unset($leaveAssign->employeeId);
                        unset($leaveAssign->leaveId);
                        $leaveAssignRepo->edit($leaveAssign, [$data['leaveId'], $data['employeeId']]);
                    }

                    $responseData = [
                        "success" => true,
                        "data" => $postedData
                    ];
                    break;
                case "pullHolidayList":
                    $holidayRepository = new HolidayRepository($this->adapter);
                    $filtersId = $postedData->id;
                    $resultSet = $holidayRepository->filterRecords($filtersId['holidayId'],$filtersId['branchId'],$filtersId['genderId']);

                    $tempArray = [];
                    foreach ($resultSet as $item) {
                        array_push($tempArray, $item);
                    }
                    $responseData = [
                        "success" => true,
                        "data" => $tempArray
                    ];
                    break;
                default:
                    $responseData = [
                        "success" => false
                    ];
                    break;
            }
        } else {
            $responseData = [
                "success" => false
            ];
        }
        return new JsonModel(['data' => $responseData]);
    }

    public function districtAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost()->id;
            $jsonModel = new JsonModel([
                'data' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_DISTRICTS, ["ZONE_ID" => $id])
            ]);
            return $jsonModel;
        } else {

        }
    }

    public function municipalityAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost()->id;
            return new JsonModel([
                'data' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_VDC_MUNICIPALITY, ["DISTRICT_ID" => $id])
            ]);
        } else {

        }
    }

    public function branchListAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $id = $request->getPost()->id;
            return new JsonModel([
                'data'=>ApplicationEntityHelper::getColumnsList($this->adapter,$id,"BRANCH_ID", ["BRANCH_NAME"])
            ]);
        }
    }
}