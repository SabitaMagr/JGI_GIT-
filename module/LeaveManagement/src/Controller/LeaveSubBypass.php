<?php

namespace LeaveManagement\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\HrisQuery;
use LeaveManagement\Model\LeaveMaster;
use LeaveManagement\Model\LeaveSubManBypass;
use LeaveManagement\Repository\LeaveSubBypassRepository;
use TheSeer\Tokenizer\Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\View\Model\JsonModel;

class LeaveSubBypass extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(LeaveSubBypassRepository::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $rawList = $this->repository->getEmployeeList($data['data']);
                $list = Helper::extractDbData($rawList);
                return new JsonModel([
                    "success" => true,
                    "data" => $list,
                    "message" => null,
                ]);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
            }
        }


        $leaveList = HrisQuery::singleton()
                ->setAdapter($this->adapter)
                ->setTableName(LeaveMaster::TABLE_NAME)
                ->setColumnList([LeaveMaster::LEAVE_ID, LeaveMaster::LEAVE_ENAME])
                ->setWhere([LeaveMaster::STATUS => 'E'])
                ->setOrder([LeaveMaster::LEAVE_ENAME => Select::ORDER_ASCENDING])
                ->setKeyValue(LeaveMaster::LEAVE_ID, LeaveMaster::LEAVE_ENAME)
                ->result();
        $config = [
            'name' => 'leave',
            'id' => 'leaveId',
            'class' => 'form-control',
            'label' => 'Type'
        ];
        $leaveSE = $this->getSelectElement($config, $leaveList);


        return $this->stickFlashMessagesTo([
                    'leaveFormElement' => $leaveSE,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }
    
    
    public function assignAction(){
            try {
                $request = $this->getRequest();
                $data = $request->getPost();
                
                $employeeId=$data['employeeId'];
                $isChecked=$data['isChecked'];
                $leaveId=$data['leaveId'];
                
                
                $leaveSubManBypassModel= new LeaveSubManBypass();
                $leaveSubManBypassModel->employeeId=$employeeId;
                $leaveSubManBypassModel->leaveId=$leaveId;
                
                    $this->repository->delete($employeeId,$leaveId);
                if($isChecked=='true'){
                    $this->repository->add($leaveSubManBypassModel);
                }
                
                return new JsonModel([
                    "success" => true,
//                    "data" => $list,
                    "message" => null,
                ]);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
            }
    }
    

}
