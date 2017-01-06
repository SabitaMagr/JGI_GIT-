<?php
namespace SelfService\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use SelfService\Model\LoanRequest;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Setup\Model\HrEmployees;
use Setup\Model\Loan;

class LoanRequestRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(LoanRequest::TABLE_NAME,$adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([LoanRequest::STATUS=>'C'],[LoanRequest::LOAN_REQUEST_ID=>$id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(LR.LOAN_DATE, 'DD-MON-YYYY') AS LOAN_DATE"),
            new Expression("LR.STATUS AS STATUS"),
            new Expression("TO_CHAR(LR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE"),
            new Expression("LR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("LR.LOAN_REQUEST_ID AS LOAN_REQUEST_ID"),
            new Expression("LR.REASON AS REASON"),
            new Expression("LR.APPROVED_AMOUNT AS APPROVED_AMOUNT"),
            new Expression("LR.LOAN_ID AS LOAN_ID") 
                ], true);

        $select->from(['LR' => LoanRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.".HrEmployees::EMPLOYEE_ID."=LR.".LoanRequest::EMPLOYEE_ID, [HrEmployees::FIRST_NAME,HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME])
                ->join(['L' => Loan::TABLE_NAME], "L.".Loan::LOAN_ID."=LR.".LoanRequest::LOAN_ID, [Loan::LOAN_CODE, Loan::LOAN_NAME]);

        $select->where([
            "LR.LOAN_REQUEST_ID=" . $id
        ]);
        $select->order("LR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    public function getAllByEmployeeId($employeeId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TO_CHAR(LR.LOAN_DATE, 'DD-MON-YYYY') AS LOAN_DATE"),
            new Expression("LR.STATUS AS STATUS"),
            new Expression("TO_CHAR(LR.REQUESTED_DATE, 'DD-MON-YYYY') AS REQUESTED_DATE"),
            new Expression("LR.REQUESTED_AMOUNT AS REQUESTED_AMOUNT"),
            new Expression("LR.LOAN_REQUEST_ID AS LOAN_REQUEST_ID"),
            new Expression("LR.REASON AS REASON"),
            new Expression("LR.APPROVED_AMOUNT AS APPROVED_AMOUNT"),
            new Expression("LR.LOAN_ID AS LOAN_ID") 
                ], true);

        $select->from(['LR' => LoanRequest::TABLE_NAME])
                ->join(['E' => HrEmployees::TABLE_NAME], "E.".HrEmployees::EMPLOYEE_ID."=LR.".LoanRequest::EMPLOYEE_ID, [HrEmployees::FIRST_NAME,HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME])
                ->join(['L' => Loan::TABLE_NAME], "L.".Loan::LOAN_ID."=LR.".LoanRequest::LOAN_ID, [Loan::LOAN_CODE, Loan::LOAN_NAME]);

        $select->where([
            "E.EMPLOYEE_ID=" . $employeeId
        ]);
        $select->order("LR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
//        $list = [];
//        foreach($result  as $row){
//            array_push($list, $row);
//        }
        return $result;
    }

}