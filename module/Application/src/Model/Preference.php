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
    public $linkTravelToSynergy = 'N';
    public $formCode = null;
    public $drAccCode = null;
    public $crAccCode = null;
    public $excessCrAccCode = null;
    public $lessDrAccCode = null;

    CONST ALLOW_SYSTEM_ATTENANCE = "ALLOW_SYSTEM_ATTENDANCE";
    CONST NEED_APPROVAL_FOR_LATE_CHECK_IN = "NEED_APPROVAL_FOR_LATE_CHECK";
    CONST ALLOW_ACCOUNT_LOCK = "ALLOW_ACCOUNT_LOCK";
    CONST ACCOUNT_LOCK_TRY_NUMBER = "ACCOUNT_LOCK_TRY_NUMBER";
    CONST ACCOUNT_LOCK_TRY_SECOND = "ACCOUNT_LOCK_TRY_SECOND";
    CONST FORCE_PASSWORD_RENEW = "FORCE_PASSWORD_RENEW";
    CONST FORCE_PASSWORD_RENEW_DAY = "FORCE_PASSWORD_RENEW_DAY";
    CONST SHOW_ADDRESS_BOOK = "SHOW_ADDRESS_BOOK";
    CONST NOTICE_TYPE = "NOTICE_TYPE";
    CONST FORM_CODE = "FORM_CODE";
    CONST LINK_TRAVEL_TO_SYNERGY = "LINK_TRAVEL_TO_SYNERGY";
    CONST DR_ACC_CODE = "DR_ACC_CODE";
    CONST CR_ACC_CODE = "CR_ACC_CODE";
    CONST EXCESS_CR_ACC_CODE = "EXCESS_CR_ACC_CODE";
    CONST LESS_DR_ACC_CODE = "LESS_DR_ACC_CODE";

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
        'linkTravelToSynergy' => self::LINK_TRAVEL_TO_SYNERGY,
        'formCode' => self::FORM_CODE,
        'drAccCode' => self::DR_ACC_CODE,
        'crAccCode' => self::CR_ACC_CODE,
        'excessCrAccCode' => self::EXCESS_CR_ACC_CODE,
        'lessDrAccCode  ' => self::LESS_DR_ACC_CODE,
    ];

}
