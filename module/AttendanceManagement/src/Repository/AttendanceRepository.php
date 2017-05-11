<?php
namespace AttendanceManagement\Repository;

use Application\Repository\RepositoryInterface;
use AttendanceManagement\Model\Attendance;
use Zend\Db\Adapter\AdapterInterface;
use Application\Model\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Application\Helper\EntityHelper;

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
    public function fetchAllByEmpIdAttendanceDt($employeeId,$attendanceDt){
        $result = $this->tableGateway->select(function(Select $select)use($employeeId,$attendanceDt){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Attendance::class, null, [Attendance::ATTENDANCE_DT], [Attendance::ATTENDANCE_TIME]),false);
            $select->where([
                    Attendance::EMPLOYEE_ID=>$employeeId, 
                    Attendance::ATTENDANCE_DT." = TO_DATE('" . $attendanceDt . "','DD-MON-YYYY')"
                ]);
            $select->order(Attendance::ATTENDANCE_TIME." ASC");
        });
        return $result;
    }
    public function getTotalByEmpIdAttendanceDt($employeeId, $attendanceDt){
        $sql = "select round(total_mins/60,0)||':'||mod(total_mins,60) total_hrs, total_mins from(
SELECT sum(abs(extract( hour from diff ))*60 +
           abs(extract( minute from diff )))  total_mins  FROM (
SELECT row_number() over ( order by A.ATTENDANCE_TIME ) as rnum,mod((row_number() over ( order by A.ATTENDANCE_TIME )),2) as num, A.EMPLOYEE_ID,A.IP_ADDRESS, A.ATTENDANCE_DT,A.ATTENDANCE_TIME,
(A.ATTENDANCE_TIME - LAG(A.ATTENDANCE_TIME) OVER (ORDER BY A.ATTENDANCE_TIME))  AS diff
  FROM HRIS_ATTENDANCE A 
  WHERE 
  A.EMPLOYEE_ID = ".$employeeId."
AND A.ATTENDANCE_DT = TO_DATE('".$attendanceDt."','DD-MON-YYYY')
) WHERE mod(rnum,2)=0)

";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }
}