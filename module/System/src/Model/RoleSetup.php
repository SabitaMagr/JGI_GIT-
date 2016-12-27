<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 2:54 PM
 */
namespace System\Model;

use Application\Model\Model;

class RoleSetup extends Model {

    const TABLE_NAME="HR_ROLES";
    const ROLE_ID="ROLE_ID";
    const ROLE_NAME="ROLE_NAME";
    const STATUS="STATUS";
    const REMARKS="REMARKS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";
    const CREATED_BY = "CREATED_BY";
    const MODIFIED_BY = "MODIFIED_BY";

    public $roleId;
    public $roleName;
    public $status;
    public $remarks;
    public $createdDt;
    public $modifiedDt;
    public $createdBy;
    public $modifiedBy;

    public $mappings=[
        'roleId'=>Self::ROLE_ID,
        'roleName'=>Self::ROLE_NAME,
        'status'=>Self::STATUS,
        'remarks'=>Self::REMARKS,
        'createdDt'=>Self::CREATED_DT,
        'modifiedDt'=>Self::MODIFIED_DT,
        'createdBy' => self::CREATED_BY,
        'modifiedBy' => self::MODIFIED_BY
        ];
}