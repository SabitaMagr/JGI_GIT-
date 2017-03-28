<?php

namespace Notification\Model;

use Application\Model\Model;

class NewsModel extends Model {
    const TABLE_NAME = 'HRIS_NEWS';

    const NEWS_DATE = 'NEWS_DATE';
    const NEWS_TYPE = 'NEWS_TYPE';
    const NEWS_TITLE = 'NEWS_TITLE';
    const NEWS_EDESC = 'NEWS_EDESC';
    const NEWS_LDESC = 'NEWS_LDESC';
    const REMARKS = 'REMARKS';
    const COMPANY_ID = 'COMPANY_ID';
    const BRANCH_ID = 'BRANCH_ID';
    const DESIGNATION_ID = 'DESIGNATION_ID';
    const DEPARTMENT_ID = 'DEPARTMENT_ID';
    const CREATED_BY = 'CREATED_BY';
    const CREATED_DT = 'CREATED_DT';
    const MODIFIED_BY = 'MODIFIED_BY';
    const MODIFIED_DT = 'MODIFIED_DT';
    const APPROVED_BY = 'APPROVED_BY';
    const APPROVED_DT = 'APPROVED_DT';
    const STATUS = 'STATUS';

    public $newsDate;
    public $newsType;
    public $newsTitle;
    public $newsEdesc;
    public $newsLdesc;
    public $remarks;
    public $companyId;
    public $branchId;
    public $designationId;
    public $departmentId;
    public $createdBy;
    public $createdDt;
    public $modifiedBy;
    public $modifiedDt;
    public $approvedBy;
    public $approvedDt;
    public $status;

    public $mappings = [
    'newsDate' => self::NEWS_DATE,
    'newsType' => self::NEWS_TYPE,
    'newsTitle' => self::NEWS_TITLE,
    'newsEdesc' => self::NEWS_EDESC,
    'newsLdesc' => self::NEWS_LDESC,
    'remarks' => self::REMARKS,
    'companyId' => self::COMPANY_ID,
    'branchId' => self::BRANCH_ID,
    'designationId' => self::DESIGNATION_ID,
    'departmentId' => self::DEPARTMENT_ID,
    'createdBy' => self::CREATED_BY,
    'createdDt' => self::CREATED_DT,
    'modifiedBy' => self::MODIFIED_DT,
    'modifiedDt' => self::MODIFIED_DT,
    'approvedBy' => self::APPROVED_BY,
    'approvedDt' => self::APPROVED_DT,
    'status' => self::STATUS,
    ];
}
