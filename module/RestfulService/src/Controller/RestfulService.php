<?php
namespace RestfulService\Controller;

use AttendanceManagement\Controller\ShiftSetup;
use AttendanceManagement\Model\ShiftAssign;
use AttendanceManagement\Repository\ShiftAssignRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;


class RestfulService extends AbstractRestfulController
{

    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function convertResultInterfaceIntoArray(ResultInterface $result)
    {
        $tempArray = [];
        foreach ($result as $unit) {
            array_push($tempArray, $unit);
        }
        return $tempArray;
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $responseData = [];
        if ($request->isPost()) {
            $postedData = $request->getPost();
            switch ($postedData->action) {
                case "pullEmployeeForShiftAssign":
                    $ids = $postedData->id;
                    $shiftAssignRepo = new ShiftAssignRepository($this->adapter);
                    $result = $shiftAssignRepo->filter($ids['branchId'], $ids['departmentId'], $ids['designationId'], $ids['positionId'], $ids['serviceTypeId']);

                    $tempArray = [];
                    foreach ($result as $item) {
                        $tmp = $shiftAssignRepo->filterByEmployeeId($item['EMPLOYEE_ID']);
                        if($tmp!=null){
                            $item[ShiftAssign::SHIFT_ID]=$tmp->{ShiftAssign::SHIFT_ID};
                            $item[\AttendanceManagement\Model\ShiftSetup::SHIFT_ENAME]=$tmp->{\AttendanceManagement\Model\ShiftSetup::SHIFT_ENAME};
                        }else{
                            $item[ShiftAssign::SHIFT_ID]="";
                            $item[\AttendanceManagement\Model\ShiftSetup::SHIFT_ENAME]="";
                        }
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
}