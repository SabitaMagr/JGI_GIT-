<?php

namespace Setup\Controller;

use LeaveManagement\Repository\LeaveAssignRepository;
use Setup\Helper\EntityHelper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;

class WebServiceController extends AbstractActionController
{
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/webService');
        }, 100);
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
        return ['data'=>$responseData];
    }

    public function districtAction()
    {

        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost()->id;
            return
                [
                    'data' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_DISTRICTS, ["ZONE_ID" => $id])
                ];
        } else {

        }
    }

    public function municipalityAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost()->id;
            return
                [
                    'data' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_VDC_MUNICIPALITY, ["DISTRICT_ID" => $id])
                ];
        } else {

        }
    }

}