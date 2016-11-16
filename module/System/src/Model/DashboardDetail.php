<?php

namespace System\Model;

use Application\Model\Model;

class DashboardDetail extends Model {

    const TABLE_NAME = "HR_DASHBOARD_DETAIL";
    const DASHBOARD_DETAIL_ID = "DASHBOARD_DETAIL_ID";
    const ROLE_ID = "ROLE_ID";
    const DASHBOARD = "DASHBOARD";
    const ROLE_TYPE = "ROLE_TYPE";

    public $dashboardDetailId;
    public $roleId;
    public $dashboard;
    public $roleType;
    public $mappings = [
        'dashboardDetailId' => self::DASHBOARD_DETAIL_ID,
        'roleId' => self::ROLE_ID,
        'dashboard' => self::DASHBOARD,
        'roleType' => self::ROLE_TYPE
    ];

}
