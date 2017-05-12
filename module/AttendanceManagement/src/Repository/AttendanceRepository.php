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
        $sql = " SELECT ROUND(TOTAL_MINS/60,0)
  ||':'
  ||MOD(TOTAL_MINS,60) TOTAL_HRS,
  TOTAL_MINS,
  HR_TYPE
FROM
  (SELECT
    CASE MOD(RNUM,2)
      WHEN 0
      THEN 'WORKING'
      ELSE 'NON-WORKING'
    END AS HR_TYPE,
    SUM(ABS(EXTRACT( HOUR FROM DIFF ))*60 + ABS(EXTRACT( MINUTE FROM DIFF ))) TOTAL_MINS
  FROM
    (SELECT ROW_NUMBER() OVER ( ORDER BY A.ATTENDANCE_TIME )    AS RNUM,
      MOD((ROW_NUMBER() OVER ( ORDER BY A.ATTENDANCE_TIME )),2) AS NUM,
      A.EMPLOYEE_ID,
      A.IP_ADDRESS,
      A.ATTENDANCE_DT,
      A.ATTENDANCE_TIME,
      (A.ATTENDANCE_TIME - LAG(A.ATTENDANCE_TIME) OVER (ORDER BY A.ATTENDANCE_TIME)) AS DIFF
    FROM HRIS_ATTENDANCE A
    WHERE A.EMPLOYEE_ID = 7
    AND A.ATTENDANCE_DT = TO_DATE('10-May-2017','DD-MON-YYYY')
    )
  GROUP BY MOD(RNUM,2)
  )";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        foreach ($result as $row ) {
          $list[$row['HR_TYPE']]=$row;
        }
        return $list;
//        print "<pre>";
//        print_r($list); die;
    }
}