<?php
namespace AttendanceManagement\Repository;

use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\Attendance;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Zend\Db\TableGateway\TableGateway;

class AttendanceRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(Attendance::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $combo) {
        $tempArray = $model->getArrayCopyForDB();
        unset($tempArray[Attendance::EMPLOYEE_ID]);
        unset($tempArray[Attendance::ATTENDANCE_DT]);
        $this->tableGateway->update($tempArray,
                [
                    Attendance::EMPLOYEE_ID=>$combo['employeeId'],
                    Attendance::ATTENDANCE_DT=>$combo['attendanceDt']
                ]);
    }

    public function fetchAll() {
        
    }

    public function fetchById($combo) {
        $result = $this->tableGateway->select(
                [
                    Attendance::EMPLOYEE_ID=>$combo['employeeId'], 
                    Attendance::ATTENDANCE_DT=>$combo['attendanceDt']
                ]);
        return $result->current();
    }
}