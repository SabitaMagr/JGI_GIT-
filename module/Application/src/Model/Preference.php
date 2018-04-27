<?php

namespace Application\Model;

class Preference extends Model {

    public $allowSystemAttendance = 'Y';
    public $needApprovalForLateCheckIn = 'N';
    public $allowAccountLock = 'N';
    public $accountLockTryNumber = 5;
    public $accountLockTrySecond = 3600;
    public $forcePasswordRenew = 'N';
    public $forcePasswordRenewDay = 45;
    public $showAddressBook = 'Y';
    public $noticeType = 'M'; //['S','M']
    public $oldPayslipType = 'N';
    public $latePenaltyLeaveDeduction = 0.5;
    public $enablePrevMthLeaveReq = 'Y';
    public $includeDayoffAsLeave = 'Y';
    public $includeHolidayAsLeave = 'Y';
    public $includeEmployeeCode = 'Y';
    public $includeCompany = 'Y';
    public $includeBranch = 'Y';

    CONST ALLOW_SYSTEM_ATTENANCE = "ALLOW_SYSTEM_ATTENDANCE";
    CONST NEED_APPROVAL_FOR_LATE_CHECK_IN = "NEED_APPROVAL_FOR_LATE_CHECK";
    CONST ALLOW_ACCOUNT_LOCK = "ALLOW_ACCOUNT_LOCK";
    CONST ACCOUNT_LOCK_TRY_NUMBER = "ACCOUNT_LOCK_TRY_NUMBER";
    CONST ACCOUNT_LOCK_TRY_SECOND = "ACCOUNT_LOCK_TRY_SECOND";
    CONST FORCE_PASSWORD_RENEW = "FORCE_PASSWORD_RENEW";
    CONST FORCE_PASSWORD_RENEW_DAY = "FORCE_PASSWORD_RENEW_DAY";
    CONST SHOW_ADDRESS_BOOK = "SHOW_ADDRESS_BOOK";
    CONST NOTICE_TYPE = "NOTICE_TYPE";
    CONST OLD_PAYSLIP_TYPE = "OLD_PAYSLIP_TYPE";
    CONST LATE_PENALTY_LEAVE_DEDUCTION = "LATE_PENALTY_LEAVE_DEDUCTION";
    CONST ENABLE_PREV_MTH_LEAVE_REQ = "ENABLE_PREV_MTH_LEAVE_REQ";
    CONST INCLUDE_DAYOFF_AS_LEAVE = "INCLUDE_DAYOFF_AS_LEAVE";
    CONST INCLUDE_HOLIDAY_AS_LEAVE = "INCLUDE_HOLIDAY_AS_LEAVE";
    CONST INCLUDE_EMPLOYEE_CODE = "INCLUDE_EMPLOYEE_CODE";
    CONST INCLUDE_COMPANY= "INCLUDE_COMPANY";
    CONST INCLUDE_BRANCH= "INCLUDE_BRANCH";

    public $mappings = [
        'allowSystemAttendance' => self::ALLOW_SYSTEM_ATTENANCE,
        'needApprovalForLateCheckIn' => self::NEED_APPROVAL_FOR_LATE_CHECK_IN,
        'allowAccountLock' => self::ALLOW_ACCOUNT_LOCK,
        'accountLockTryNumber' => self::ACCOUNT_LOCK_TRY_NUMBER,
        'accountLockTrySecond' => self::ACCOUNT_LOCK_TRY_SECOND,
        'forcePasswordRenew' => self::FORCE_PASSWORD_RENEW,
        'forcePasswordRenewDay' => self::FORCE_PASSWORD_RENEW_DAY,
        'showAddressBook' => self::SHOW_ADDRESS_BOOK,
        'noticeType' => self::NOTICE_TYPE,
        'oldPayslipType' => self::OLD_PAYSLIP_TYPE,
        'latePenaltyLeaveDeduction' => self::LATE_PENALTY_LEAVE_DEDUCTION,
        'enablePrevMthLeaveReq' => self::ENABLE_PREV_MTH_LEAVE_REQ,
        'includeDayoffAsLeave' => self::INCLUDE_DAYOFF_AS_LEAVE,
        'includeHolidayAsLeave' => self::INCLUDE_HOLIDAY_AS_LEAVE,
        'includeEmployeeCode' => self::INCLUDE_EMPLOYEE_CODE,
        'includeCompany' => self::INCLUDE_COMPANY,
        'includeBranch' => self::INCLUDE_BRANCH,
    ];

}
