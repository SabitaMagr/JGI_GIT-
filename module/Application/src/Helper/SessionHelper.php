<?php

namespace Application\Helper;

use Setup\Model\EmployeeFile;
use Setup\Model\HrEmployees;
use Setup\Model\Company;
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
            $tempEmployeeFileData = EntityHelper::getTableKVList($adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::PROFILE_PICTURE_ID], [HrEmployees::EMPLOYEE_ID => $employeeId], null);
            $employeeFileId = (sizeof($tempEmployeeFileData) == 0) ? null : $tempEmployeeFileData[$employeeId];
            $employeeName = EntityHelper::getTableKVList($adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME], [HrEmployees::EMPLOYEE_ID => $employeeId], null)[$employeeId];
            //start to set company logo details
            $companyId = EntityHelper::getTableKVList($adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::COMPANY_ID], [HrEmployees::EMPLOYEE_ID => $employeeId], null)[$employeeId];
            $companyName = EntityHelper::getTableKVList($adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], [Company::COMPANY_ID => $companyId], null)[$companyId];
            $companyAddress = EntityHelper::getTableKVList($adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::ADDRESS], [Company::COMPANY_ID => $companyId], null)[$companyId];
            $event->getViewModel()->setVariable("companyName", $companyName);
            $event->getViewModel()->setVariable("companyAddress", $companyAddress);
            $companyLogoCode = EntityHelper::getTableKVList($adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::LOGO], [Company::COMPANY_ID => $companyId], null)[$companyId];
            //end to set companu logo details
            $event->getViewModel()->setVariable("employeeName", $employeeName);
            if ($employeeFileId != null) {
                $filePath = EntityHelper::getTableKVList($adapter, EmployeeFile::TABLE_NAME, EmployeeFile::FILE_CODE, [EmployeeFile::FILE_PATH], [EmployeeFile::FILE_CODE => $employeeFileId], null)[$employeeFileId];
                $event->getViewModel()->setVariable("profilePictureUrl", $filePath);
            } else {
                $config = $app->getServiceManager()->get('config');
                $event->getViewModel()->setVariable("profilePictureUrl", $config['default-profile-picture']);
            }
            
            if ($companyLogoCode != null) {
                $companyImageFilePath= EntityHelper::getTableKVList($adapter, EmployeeFile::TABLE_NAME, EmployeeFile::FILE_CODE, [EmployeeFile::FILE_PATH], [EmployeeFile::FILE_CODE => $companyLogoCode], null)[$companyLogoCode];
                $event->getViewModel()->setVariable("companyLogoUrl", $companyImageFilePath);
            } else {
                $config = $app->getServiceManager()->get('config');
                $event->getViewModel()->setVariable("companyLogoUrl", "NO");
            }
            
        }
    }

}
