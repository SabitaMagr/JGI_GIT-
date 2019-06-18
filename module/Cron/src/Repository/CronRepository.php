<?php

namespace Cron\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;

class CronRepository {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchEmailList() {
        $sql = "select e.EMAIL_OFFICIAL,ad.ATTENDANCE_DT 
            from HRIS_EMPLOYEES e 
            join HRIS_ATTENDANCE_DETAIL ad 
            on e.EMPLOYEE_ID = ad.EMPLOYEE_ID 
            where ATTENDANCE_DT=(trunc(sysdate-0)) 
            and OVERALL_STATUS='AB'";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function fetchDataOfMissingIp($missingIp) {
        $sql = "select e.EMAIL_OFFICIAL,
            ad.BRANCH_MANAGER_ID, 
            e.FULL_NAME, 
            ad.DEVICE_IP, 
            trunc(SYSDATE-0) as ATT_DT 
            from HRIS_ATTD_DEVICE_MASTER ad 
            join HRIS_EMPLOYEES e on ad.branch_manager_id = e.EMPLOYEE_ID 
            where ad.BRANCH_MANAGER_ID = 
            (select BRANCH_MANAGER_ID from HRIS_ATTD_DEVICE_MASTER 
            where DEVICE_IP = '{$missingIp}')";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function fetchAllDeviceIp() {
        $sql = "SELECT DEVICE_IP as IP_ADDRESS from HRIS_ATTD_DEVICE_MASTER";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

    public function fetchAllAttendanceIp() {
        $sql = "SELECT DISTINCT IP_ADDRESS as IP_ADDRESS from TNEPAL_HRIS_APR2.HRIS_ATTENDANCE where ATTENDANCE_DT = TRUNC(SYSDATE-0)";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
