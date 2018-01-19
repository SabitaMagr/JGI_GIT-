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
    ];

}
