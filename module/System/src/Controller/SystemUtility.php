<?php

namespace System\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class SystemUtility extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
    }

    public function reAttendanceAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $date = Helper::getExpressionDate($data['ATTENDANCE_DATE'])->getExpression();
                $employeeId = $data['EMPLOYEE_ID'];
                EntityHelper::rawQueryResult($this->adapter, "BEGIN HRIS_REATTENDANCE({$date},{$employeeId}); END;");
                return new JsonModel(['success' => true, 'data' => null, 'message' => "Re Attendnace Sucessfull"]);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo([
                    'employeeList' => EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => "E", HrEmployees::RETIRED_FLAG => "N"])
        ]);
    }

}
