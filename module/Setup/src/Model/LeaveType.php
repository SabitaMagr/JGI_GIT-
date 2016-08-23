<?php
namespace Setup\Model;

class LeaveType extends Model{

	public $leaveId;
	public $leaveCode;

	public $leaveName;
	public $totalLeave;
	public $remarks;
	public $status;
	public $createdDt;
	public $modifiedDt;

	public $mappings = [
		'leaveId'=>'LEAVE_ID',
		'leaveCode'=>'LEAVE_CODE',
		'leaveName'=>'LEAVE_NAME',
		'totalLeave'=>'TOTAL_LEAVE',
		'remarks'=>'REMARKS',
		'status'=>'STATUS',
		'createdDt'=>'CREATED_DT',
		'modifiedDt'=>'MODIFIED_DT'
	];

}
