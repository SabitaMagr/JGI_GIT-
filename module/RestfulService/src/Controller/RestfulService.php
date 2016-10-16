<?php
namespace RestfulService\Controller;

use Application\Helper\Helper;
use AttendanceManagement\Controller\ShiftSetup;
use AttendanceManagement\Model\ShiftAssign;
use AttendanceManagement\Repository\ShiftAssignRepository;
use Payroll\Model\FlatValueDetail;
use Payroll\Model\MonthlyValueDetail;
use Payroll\Repository\FlatValueDetailRepo;
use Payroll\Repository\MonthlyValueDetailRepo;
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
                        if ($tmp != null) {
                            $item[ShiftAssign::SHIFT_ID] = $tmp[ShiftAssign::SHIFT_ID];
                            $item[\AttendanceManagement\Model\ShiftSetup::SHIFT_ENAME] = $tmp[\AttendanceManagement\Model\ShiftSetup::SHIFT_ENAME];
                        } else {
                            $item[ShiftAssign::SHIFT_ID] = "";
                            $item[\AttendanceManagement\Model\ShiftSetup::SHIFT_ENAME] = "";
                        }
                        array_push($tempArray, $item);
                    }
                    $responseData = [
                        "success" => true,
                        "data" => $tempArray
                    ];
                    break;

                case "assignEmployeeShift":
                    $data = $postedData->data;
                    $shiftAssign = new ShiftAssign();

                    $shiftAssign->employeeId = $data['employeeId'];
                    $shiftAssign->shiftId = $data['shiftId'];

                    $shiftAssignRepo = new ShiftAssignRepository($this->adapter);
                    if (!empty($data['oldShiftId'])) {
                        $shiftAssignClone = clone $shiftAssign;

                        unset($shiftAssignClone->employeeId);
                        unset($shiftAssignClone->shiftId);
                        unset($shiftAssignClone->createdDt);

                        $shiftAssignClone->status = 'D';
                        $shiftAssignClone->modifiedDt = Helper::getcurrentExpressionDate();
                        $shiftAssignRepo->edit($shiftAssignClone, [$data['employeeId'], $data['oldShiftId']]);

                        $shiftAssign->createdDt = Helper::getcurrentExpressionDate();
                        $shiftAssign->status = 'E';
                        $shiftAssignRepo->add($shiftAssign);
                    } else {
                        $shiftAssign->createdDt = Helper::getcurrentExpressionDate();
                        $shiftAssign->status = 'E';
                        $shiftAssignRepo->add($shiftAssign);

                    }

                    $responseData = [
                        "success" => true,
                        "data" => $postedData
                    ];
                    break;

                case "pullEmployeeMonthlyValue":
                    $data = $postedData->id;
                    $monValDetRepo = new MonthlyValueDetailRepo($this->adapter);
                    $empListRaw = $monValDetRepo->fetchEmployees($data['branch'], $data['department'], $data['designation']);
                    $empListP = [];
                    foreach ($empListRaw as $key => $emp) {
                        $empListP[$key] = $emp;
                    }
                    $empList = [];
                    $mthIds = $data['monthlyValues'];

                    $mthVal = [];
                    foreach ($mthIds as $mthId) {
                        $tempData = $monValDetRepo->filter($data['branch'], $data['department'], $data['designation'], $mthId);
                        $tempOutput = [];
                        foreach ($tempData as $key => $val) {
                            $val['MTH_ID'] = $mthId;
                            array_push($tempOutput, $val);
                        }
                        array_push($mthVal, $tempOutput);

                    }
//                    foreach ($empListRaw as $key => $val) {
//
//                        foreach ($mthVal as $mthValUnit) {
//                            print "a";
//                            print_r($mthValUnit);
//                            print "b";
//                            print_r($val);
//                            if (array_key_exists($key - 1, $mthValUnit)) {
//                                $val[$mthValUnit[$key - 1]['MTH_ID']] = floatval(($mthValUnit[$key - 1]['MTH_VALUE'] == null) ? 0 : $mthValUnit[$key - 1]['MTH_VALUE']);
//                            }
//                        }
//                        $empList[$key] = $val;
//                    }
                    $counter = 0;
                    foreach ($mthVal as $mthValUnit) {
                        foreach ($empListP as $key => $val) {
                            if ($counter == 0) {
                                $empList[$key] = $val;
                            }
                            foreach ($mthValUnit as $key1 => $val1) {
                                if ($val['EMPLOYEE_ID'] == $val1['EMPLOYEE_ID']) {
                                    $empList[$key][$val1['MTH_ID']] =  floatval($val1['MTH_VALUE']);

                                    break;
                                } else {
                                    $empList[$key][$val1['MTH_ID']] = floatval("0");
                                }
                            }
                        }
                        $counter++;
                    }
                    $responseData = [
                        "success" => true,
                        "data" => $empList
                    ];

                    break;
                case "pushEmployeeMonthlyValue":
                    $data = $postedData->id;
                    $monValDet = new MonthlyValueDetail();
                    $monValDet->employeeId = $data['employeeId'];
                    $monValDet->mthId = $data['mthId'];
                    $monValDet->mthValue = $data['value'];
                    unset($monValDet->branchId);
                    unset($monValDet->companyId);

                    $monValDetRepo = new MonthlyValueDetailRepo($this->adapter);
                    if ($monValDetRepo->fetchById([$data['employeeId'], $data['mthId']]) == null) {
                        $monValDet->createdDt = Helper::getcurrentExpressionDate();
                        $monValDet->status = 'E';
                        $monValDetRepo->add($monValDet);
                    } else {
                        unset($monValDet->status);
                        unset($monValDet->createdDt);
                        unset($monValDet->employeeId);
                        unset($monValDet->mthId);
                        $monValDet->modifiedDt = Helper::getcurrentExpressionDate();
                        $monValDetRepo->edit($monValDet, [$data['employeeId'], $data['mthId']]);
                    }

                    $responseData = [
                        "success" => true,
                        "data" => $data
                    ];
                    break;

                case "pullEmployeeFlatValue":
                    $data = $postedData->id;
                    $flatValDetRepo = new FlatValueDetailRepo($this->adapter);
                    $empListRaw = $flatValDetRepo->fetchEmployees($data['branch'], $data['department'], $data['designation']);
                    $empListP = [];
                    foreach ($empListRaw as $key => $emp) {
                        $empListP[$key] = $emp;
                    }
                    $empList = [];
                    $mthIds = $data['flatValues'];

                    $mthVal = [];
                    foreach ($mthIds as $mthId) {
                        $tempData = $flatValDetRepo->filter($data['branch'], $data['department'], $data['designation'], $mthId);
                        $tempOutput = [];
                        foreach ($tempData as $key => $val) {
                            $val['MTH_ID'] = $mthId;
                            array_push($tempOutput, $val);
                        }
                        array_push($mthVal, $tempOutput);

                    }
                    $counter = 0;
                    foreach ($mthVal as $mthValUnit) {
                        foreach ($empListP as $key => $val) {
                            if ($counter == 0) {
                                $empList[$key] = $val;
                            }
                            foreach ($mthValUnit as $key1 => $val1) {
                                if ($val['EMPLOYEE_ID'] == $val1['EMPLOYEE_ID']) {
                                    $empList[$key][$val1['FLAT_ID']] =  floatval($val1['FLAT_VALUE']);

                                    break;
                                } else {
                                    $empList[$key][$val1['FLAT_ID']] = floatval("0");
                                }
                            }
                        }
                        $counter++;
                    }
                    $responseData = [
                        "success" => true,
                        "data" => $empList
                    ];

                    break;
                case "pushEmployeeFlatValue":
                    $data = $postedData->id;
                    $flatValDet = new FlatValueDetail();
                    $flatValDet->employeeId = $data['employeeId'];
                    $flatValDet->flatId = $data['flatId'];
                    $flatValDet->flatValue = $data['value'];
                    unset($flatValDet->branchId);
                    unset($flatValDet->companyId);

                    $flatValDetRepo = new FlatValueDetailRepo($this->adapter);
                    if ($flatValDetRepo->fetchById([$data['employeeId'], $data['flatId']]) == null) {
                        $flatValDet->createdDt = Helper::getcurrentExpressionDate();
                        $flatValDet->status = 'E';
                        $flatValDetRepo->add($flatValDet);
                    } else {
                        unset($flatValDet->status);
                        unset($flatValDet->createdDt);
                        unset($flatValDet->employeeId);
                        unset($flatValDet->flatId);
                        $flatValDet->modifiedDt = Helper::getcurrentExpressionDate();
                        $flatValDetRepo->edit($flatValDet, [$data['employeeId'], $data['flatId']]);
                    }

                    $responseData = [
                        "success" => true,
                        "data" => $data
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