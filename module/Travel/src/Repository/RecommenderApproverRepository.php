<?php
namespace Travel\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Travel\Model\RecommenderApprover;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class RecommenderApproverRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(RecommenderApprover::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }
    
    public function fetchAll() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("RA.STATUS AS STATUS"),
            new Expression("RA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("RA.RECOMMEND_BY AS RECOMMEND_BY"),
            new Expression("RA.APPROVED_BY AS APPROVED_BY"),
                ], true);
        $select->from(['RA' => RecommenderApprover::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=RA.EMPLOYEE_ID", ['FIRST_NAME' => new Expression("INITCAP(E.FIRST_NAME)"), 'MIDDLE_NAME' => new Expression("INITCAP(E.MIDDLE_NAME)"), 'LAST_NAME' => new Expression("INITCAP(E.LAST_NAME)")], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=RA.RECOMMEND_BY", ['FIRST_NAME_R' => new Expression("INITCAP(E1.FIRST_NAME)"), "MIDDLE_NAME_R" => new Expression("INITCAP(E1.MIDDLE_NAME)"), "LAST_NAME_R" => new Expression("INITCAP(E1.LAST_NAME)"), "RETIRED_R" => "RETIRED_FLAG", "STATUS_R" => "STATUS"], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=RA.APPROVED_BY", ['FIRST_NAME_A' => new Expression("INITCAP(E2.FIRST_NAME)"), "MIDDLE_NAME_A" => new Expression("INITCAP(E2.MIDDLE_NAME)"), "LAST_NAME_A" => new Expression("INITCAP(E2.LAST_NAME)"), "RETIRED_A" => "RETIRED_FLAG", "STATUS_A" => "STATUS"], "left");

        $select->where([
            "RA.STATUS='E'",
            "E.STATUS='E'",
            "E.RETIRED_FLAG='N' AND
  (((E1.STATUS =
    CASE
      WHEN E1.STATUS IS NOT NULL
      THEN ('E')
    END
  OR E1.STATUS IS NULL)
  AND
  (E1.RETIRED_FLAG =
    CASE
      WHEN E1.RETIRED_FLAG IS NOT NULL
      THEN ('N')
    END
  OR E1.RETIRED_FLAG IS NULL))
OR
  ((E2.STATUS =
    CASE
      WHEN E2.STATUS IS NOT NULL
      THEN ('E')
    END
  OR E2.STATUS IS NULL)
AND
  (E2.RETIRED_FLAG =
    CASE
      WHEN E2.RETIRED_FLAG IS NOT NULL
      THEN ('N')
    END
  OR E2.RETIRED_FLAG IS NULL)))"
        ]);
        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [RecommenderApprover::EMPLOYEE_ID => $id]);
    }

    public function delete($id) {
        $this->tableGateway->update([RecommenderApprover::STATUS => 'D'], [RecommenderApprover::EMPLOYEE_ID => $id]);
    }

    public function fetchById($id) {
        $row = $this->tableGateway->select([RecommenderApprover::EMPLOYEE_ID => $id]);
        return $row->current();
    }
    public function getDetailByEmployeeID($employeeId, $recommenderId = null, $approverId = null) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("RA.STATUS AS STATUS"),
            new Expression("RA.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("RA.RECOMMEND_BY AS RECOMMEND_BY"),
            new Expression("RA.APPROVED_BY AS APPROVED_BY"),
                ], true);
        $select->from(['RA' => RecommenderApprover::TABLE_NAME])
                ->join(['E' => "HRIS_EMPLOYEES"], "E.EMPLOYEE_ID=RA.EMPLOYEE_ID", ['FIRST_NAME' => new Expression("INITCAP(E.FIRST_NAME)"), 'MIDDLE_NAME' => new Expression("INITCAP(E.MIDDLE_NAME)"), 'LAST_NAME' => new Expression("INITCAP(E.LAST_NAME)")], "left")
                ->join(['E1' => "HRIS_EMPLOYEES"], "E1.EMPLOYEE_ID=RA.RECOMMEND_BY", ['FIRST_NAME_R' => new Expression("INITCAP(E1.FIRST_NAME)"), "MIDDLE_NAME_R" => new Expression("INITCAP(E1.MIDDLE_NAME)"), "LAST_NAME_R" => new Expression("INITCAP(E1.LAST_NAME)"), "RETIRED_R" => "RETIRED_FLAG", "STATUS_R" => "STATUS"], "left")
                ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=RA.APPROVED_BY", ['FIRST_NAME_A' => new Expression("INITCAP(E2.FIRST_NAME)"), "MIDDLE_NAME_A" => new Expression("INITCAP(E2.MIDDLE_NAME)"), "LAST_NAME_A" => new Expression("INITCAP(E2.LAST_NAME)"), "RETIRED_A" => "RETIRED_FLAG", "STATUS_A" => "STATUS"], "left");

        $select->where([
            "RA.STATUS='E'",
            "E.STATUS='E'",
            "E.RETIRED_FLAG='N'",
            "RA.EMPLOYEE_ID=" . $employeeId . " AND
              (((E1.STATUS =
                CASE
                  WHEN E1.STATUS IS NOT NULL
                  THEN ('E')
                END
              OR E1.STATUS IS NULL)
              AND
              (E1.RETIRED_FLAG =
                CASE
                  WHEN E1.RETIRED_FLAG IS NOT NULL
                  THEN ('N')
                END
              OR E1.RETIRED_FLAG IS NULL))
            OR
              ((E2.STATUS =
                CASE
                  WHEN E2.STATUS IS NOT NULL
                  THEN ('E')
                END
              OR E2.STATUS IS NULL)
            AND
              (E2.RETIRED_FLAG =
                CASE
                  WHEN E2.RETIRED_FLAG IS NOT NULL
                  THEN ('N')
                END
              OR E2.RETIRED_FLAG IS NULL)))"
        ]);

        if ($recommenderId != null && $recommenderId != -1) {
            $select->where([
                "RA.RECOMMEND_BY=" . $recommenderId]);
        }

        if ($approverId != null && $approverId != -1) {
            $select->where([
                "RA.APPROVED_BY=" . $approverId]);
        }

        $select->order("E.FIRST_NAME ASC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
}
