<?php

namespace Setup\Controller;

use LeaveManagement\Repository\LeaveAssignRepository;
use Setup\Helper\EntityHelper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

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

}