<?php

namespace Application\Helper;

use Setup\Model\EmployeeFile;
use Setup\Model\HrEmployees;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;
use Zend\Db\Adapter\AdapterInterface as DbAdapterInterface;

class SessionHelper {

    public static function sessionCheck(MvcEvent $event) {
        $app = $event->getApplication();
        $adapter = $app->getServiceManager()->get(DbAdapterInterface::class);
        $auth = new AuthenticationService();
        $employeeId = $auth->getStorage()->read()['employee_id'];
        if ($employeeId != null) {
            $employeeFileId = EntityHelper::getTableKVList($adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::PROFILE_PICTURE_ID], [HrEmployees::EMPLOYEE_ID => $employeeId], null)[$employeeId];
            $employeeName = EntityHelper::getTableKVList($adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME], [HrEmployees::EMPLOYEE_ID => $employeeId], null)[$employeeId];
            if ($employeeFileId != null) {
                $filePath = EntityHelper::getTableKVList($adapter, EmployeeFile::TABLE_NAME, EmployeeFile::FILE_CODE, [EmployeeFile::FILE_PATH], [EmployeeFile::FILE_CODE => $employeeFileId], null)[$employeeFileId];
                $event->getViewModel()->setVariable("profilePictureUrl", $filePath);
                $event->getViewModel()->setVariable("employeeName", $employeeName);
            } else {
                $event->getViewModel()->setVariable("profilePictureUrl", "1480316755.jpg");
                $event->getViewModel()->setVariable("employeeName", "Nick");
            }
        }
    }

}
