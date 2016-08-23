<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 8/23/16
 * Time: 12:19 PM
 */

namespace Setup\Model;

class JobHistory extends Model
{
    public $jobHistoryId;
    public $employeeId;
    public $startDate;
    public $endDate;
    public $serviceTypeId;
    public $fromBranchId;
    public $toBranchId;
    public $fromDepartmentId;
    public $toDepartmentId;
    public $fromDesignationId;
    public $toDesignationId;
    public $fromPositionId;
    public $toPositionId;

    public $mappings = [
        'jobHistoryId' => 'JOB_HISTORY_ID',
        'employeeId' => 'EMPLOYEE_ID',
        'startDate' => 'START_DATE',
        'endDate' => 'END_DATE',
        'serviceTypeId' => 'SERVICE_TYPE_ID',
        'fromBranchId' => 'FROM_BRANCH_ID',
        'toBranchId' => 'TO_BRANCH_ID',
        'fromDepartmentId' => 'FROM_DEPARTMENT_ID',
        'toDepartmentId' => 'TO_DEPARTMENT_ID',
        'fromDesignationId' => 'FROM_DESIGNATION_ID',
        'toDesignationId' => 'TO_DESIGNATION_ID',
        'fromPositionId' => 'FROM_POSITION_ID',
        'toPositionId' => 'TO_POSITION_ID'
    ];
}