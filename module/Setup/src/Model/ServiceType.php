<?php
namespace Setup\Model;

class ServiceType extends Model{

    public $serviceTypeId;
    public $serviceTypeCode;
    public $serviceTypeName;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;

    public $mappings = [
        'serviceTypeId'=>'SERVICE_TYPE_ID',
        'serviceTypeCode'=>'SERVICE_TYPE_CODE',
        'serviceTypeName'=>'SERVICE_TYPE_NAME',
        'remarks'=>'REMARKS',
        'status'=>'STATUS',
        'createdDt'=>'CREATED_DT',
        'modifiedDt'=>'MODIFIED_DT'
    ];

}