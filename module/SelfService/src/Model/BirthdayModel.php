<?php

namespace SelfService\Model;

use Application\Model\Model;

class BirthdayModel extends Model {

    const TABLE_NAME = "BIRTHDAY_MESSAGES";
    const BIRTHDAY_ID = "BIRTHDAY_ID";
    const FROM_EMPLOYEE = "FROM_EMPLOYEE";
    const BIRTHDAY_EMPLOYEE = "BIRTHDAY_EMPLOYEE";
    const BIRTHDAY_MESSAGE = "BIRTHDAY_MESSAGE";
    const CREATED_DATE = "CREATED_DATE";
    const MODIFIED_DATE = "MODIFIED_DATE";
    const STATUS = "STATUS";

    public $birthdayId;
    public $fromEmployee;
    public $birthdayEmployee;
    public $birthdayMessage;
    public $createdDate;
    public $modifiedDate;
    public $status;
    public $mappings = [
        'birthdayId' => self::BIRTHDAY_ID,
        'fromEmployee' => self::FROM_EMPLOYEE,
        'birthdayEmployee' => self::BIRTHDAY_EMPLOYEE,
        'birthdayMessage' => self::BIRTHDAY_MESSAGE,
        'createdDate' => self::CREATED_DATE,
        'modifiedDate' => self::MODIFIED_DATE,
        'status' => self::STATUS
    ];

}
