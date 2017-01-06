<?php

namespace ManagerService\Model;

use Application\Model\Model;

class SalaryDetail extends Model {

    const TABLE_NAME = "HR_SALARY_DETAIL";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const OLD_AMOUNT = "OLD_AMOUNT";
    const NEW_AMOUNT = "NEW_AMOUNT";
    const EFFECTIVE_DATE = "EFFECTIVE_DATE";
    const JOB_HISTORY_ID = "JOB_HISTORY_ID";
    const CREATED_BY = "CREATED_BY";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_BY = "MODIFIED_BY";
    const MODIFIED_DT = "MODIFIED_DT";
    const STATUS = "STATUS";
    const SALARY_DETAIL_ID = "SALARY_DETAIL_ID";

    public $employeeId;
    public $oldAmount;
    public $newAmount;
    public $effectiveDate;
    public $jobHistoryId;
    public $createdBy;
    public $createdDt;
    public $modifiedBy;
    public $modifiedDt;
    public $status;
    public $salaryDetailId;
    public $mappings = [
        'employeeId' => self::EMPLOYEE_ID,
        'oldAmount' => self::OLD_AMOUNT,
        'newAmount' => self::NEW_AMOUNT,
        'effectiveDate' => self::EFFECTIVE_DATE,
        'jobHistoryId' => self::JOB_HISTORY_ID,
        'createdBy' => self::CREATED_BY,
        'createdDt' => self::CREATED_DT,
        'modifiedBy' => self::MODIFIED_BY,
        'modifiedDt' => self::MODIFIED_DT,
        'status' => self::STATUS,
        'salaryDetailId' => self::SALARY_DETAIL_ID
    ];

}
