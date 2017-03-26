<?php

namespace System\Model;

use Application\Model\Model;

class AttendanceDevice extends Model {

    const TABLE_NAME = "HRIS_ATTENDANCE_DEVICE";
    const DEVICE_ID = "DEVICE_ID";
    const DEVICE_NAME = "DEVICE_NAME";
    const DEVICE_IP = "DEVICE_IP";
    const DEVICE_LOCATION = "DEVICE_LOCATION";
    const ISACTIVE = "ISACTIVE";
    const COMPANY_ID = "COMPANY_ID";
    const BRANCH_ID = "BRANCH_ID";

    public $deviceId;
    public $deviceName;
    public $deviceIp;
    public $deviceLocation;
    public $isActive;
    public $companyId;
    public $branchId;
    
    
    public $mappings = [
        'deviceId' => Self::DEVICE_ID,
        'deviceName' => Self::DEVICE_NAME,
        'deviceIp' => Self::DEVICE_IP,
        'deviceLocation' => Self::DEVICE_LOCATION,
        'isActive' => Self::ISACTIVE,
        'companyId' => Self::COMPANY_ID,
        'branchId' => Self::BRANCH_ID
    ];

}
