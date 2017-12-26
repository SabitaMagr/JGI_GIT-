<?php

namespace Application\Model;

class Preference {

    public $allowSystemAttendance = true;
    public $needApprovalForLateCheckIn = false;
    public $allowAccountLock = false;
    public $accountLockTryNumber = 5;
    public $accountLockTrySecond = 3600;
    public $forcePasswordRenew = false;
    public $forcePasswordRenewDay = 45;
    public $showAddressBook = true;

}
