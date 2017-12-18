<?php

namespace Notification\Model;

use Application\Model\Model;

class NewsEmployeeModel extends Model {
    const TABLE_NAME = 'HRIS_NEWS_EMPLOYEE';

    const NEWS_ID = 'NEWS_ID';
    const EMPLOYEE_ID = 'EMPLOYEE_ID';
    

    public $newsId;
    public $employeeId;

    public $mappings = [
    'newsId' => self::NEWS_ID,
    'employeeId' => self::EMPLOYEE_ID
    ];
}
