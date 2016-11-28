<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 2:54 PM
 */
namespace System\Model;

use Application\Model\Model;

class UserSetup extends Model {

    const TABLE_NAME="HR_USERS";
    const USER_ID="USER_ID";
    const USER_NAME="USER_NAME";
    const PASSWORD="PASSWORD";
    const ROLE_ID="ROLE_ID";
    const EMPLOYEE_ID="EMPLOYEE_ID";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";

    public $userId;
    public $userName;
    public $password;
    public $roleId;
    public $employeeId;
    public $status;
    public $createdDt;
    public $modifiedDt;

    public $mappings=[
        'userId'=>Self::USER_ID,
        'userName'=>Self::USER_NAME,
        'password'=>Self::PASSWORD,
        'roleId'=>Self::ROLE_ID,
        'employeeId'=>Self::EMPLOYEE_ID,
        'status'=>Self::STATUS,
        'createdDt'=>Self::CREATED_DT,
        'modifiedDt'=>Self::MODIFIED_DT
    ];
}