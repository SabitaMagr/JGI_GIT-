<?php
namespace SelfService\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use SelfService\Model\TrainingRequest;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class TrainingRequestRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(TrainingRequest::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $currentDate = Helper::getcurrentExpressionDate();
        $this->tableGateway->update([TrainingRequest::STATUS => 'C', TrainingRequest::MODIFIED_DATE => $currentDate], [TrainingRequest::REQUEST_ID => $id]);
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchAll() {
        
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TR.REQUEST_ID AS REQUEST_ID"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY')) AS START_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY')) AS START_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY')) AS START_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY')) AS END_DATE"),
            new Expression("INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY')) AS END_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY')) AS END_DATE_BS"),
            new Expression("TR.TRAINING_ID AS TRAINING_ID"),
            new Expression("TR.TITLE AS TITLE"),
            new Expression("TR.DESCRIPTION AS DESCRIPTION"),
            new Expression("TR.DURATION AS DURATION"),
            new Expression("TR.TRAINING_TYPE AS TRAINING_TYPE"),
            new Expression("(CASE WHEN TR.TRAINING_TYPE = 'CC' THEN 'Company Contribution' ELSE 'Personal' END) AS TRAINING_TYPE_DETAIL"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("LEAVE_STATUS_DESC(TR.STATUS) AS STATUS_DETAIL"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("INITCAP(TO_CHAR(TR.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE"),
            new Expression("(CASE WHEN TR.STATUS = 'RQ' THEN 'Y' ELSE 'N' END) AS ALLOW_EDIT"),
            new Expression("(CASE WHEN TR.STATUS IN ('RQ','RC','AP') THEN 'Y' ELSE 'N' END) AS ALLOW_DELETE"),
            ], true);

        $select->from(['TR' => TrainingRequest::TABLE_NAME])
            ->join(['E' => 'HRIS_EMPLOYEES'], 'E.EMPLOYEE_ID=TR.EMPLOYEE_ID', ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
            ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=TR.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
            ->join(['E3' => "HRIS_EMPLOYEES"], "E3.EMPLOYEE_ID=TR.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E3.FULL_NAME)")], "left")
            ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=TR.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
            ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
            ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left");

        $select->where(["TR.REQUEST_ID" => $id]);
        $select->order(["TR.REQUESTED_DATE" => Select::ORDER_DESCENDING]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllByEmployeeId($employeeId): array {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns([
            new Expression("TR.REQUEST_ID AS REQUEST_ID"),
            new Expression("TR.EMPLOYEE_ID AS EMPLOYEE_ID"),
            new Expression("INITCAP(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(TR.REQUESTED_DATE, 'DD-MON-YYYY')) AS REQUESTED_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY')) AS START_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(TR.START_DATE, 'DD-MON-YYYY')) AS START_DATE_BS"),
            new Expression("INITCAP(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY')) AS END_DATE_AD"),
            new Expression("BS_DATE(TO_CHAR(TR.END_DATE, 'DD-MON-YYYY')) AS END_DATE_BS"),
            new Expression("TR.TRAINING_ID AS TRAINING_ID"),
            new Expression("TR.TITLE AS TITLE"),
            new Expression("TR.DESCRIPTION AS DESCRIPTION"),
            new Expression("TR.DURATION AS DURATION"),
            new Expression("TR.TRAINING_TYPE AS TRAINING_TYPE"),
            new Expression("(CASE WHEN TR.TRAINING_TYPE = 'CC' THEN 'Company Contribution' ELSE 'Personal' END) AS TRAINING_TYPE_DETAIL"),
            new Expression("TR.REMARKS AS REMARKS"),
            new Expression("TR.STATUS AS STATUS"),
            new Expression("LEAVE_STATUS_DESC(TR.STATUS) AS STATUS_DETAIL"),
            new Expression("TR.RECOMMENDED_BY AS RECOMMENDED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.RECOMMENDED_DATE, 'DD-MON-YYYY')) AS RECOMMENDED_DATE"),
            new Expression("TR.RECOMMENDED_REMARKS AS RECOMMENDED_REMARKS"),
            new Expression("TR.APPROVED_BY AS APPROVED_BY"),
            new Expression("INITCAP(TO_CHAR(TR.APPROVED_DATE, 'DD-MON-YYYY')) AS APPROVED_DATE"),
            new Expression("TR.APPROVED_REMARKS AS APPROVED_REMARKS"),
            new Expression("INITCAP(TO_CHAR(TR.MODIFIED_DATE, 'DD-MON-YYYY')) AS MODIFIED_DATE"),
            new Expression("(CASE WHEN TR.STATUS = 'RQ' THEN 'Y' ELSE 'N' END) AS ALLOW_EDIT"),
            new Expression("(CASE WHEN TR.STATUS IN ('RQ','RC','AP') THEN 'Y' ELSE 'N' END) AS ALLOW_DELETE"),
            ], true);

        $select->from(['TR' => TrainingRequest::TABLE_NAME])
            ->join(['E' => 'HRIS_EMPLOYEES'], 'E.EMPLOYEE_ID=TR.EMPLOYEE_ID', ["FULL_NAME" => new Expression("INITCAP(E.FULL_NAME)")], "left")
            ->join(['E2' => "HRIS_EMPLOYEES"], "E2.EMPLOYEE_ID=TR.RECOMMENDED_BY", ['RECOMMENDED_BY_NAME' => new Expression("INITCAP(E2.FULL_NAME)")], "left")
            ->join(['E3' => "HRIS_EMPLOYEES"], "E3.EMPLOYEE_ID=TR.APPROVED_BY", ['APPROVED_BY_NAME' => new Expression("INITCAP(E3.FULL_NAME)")], "left")
            ->join(['RA' => "HRIS_RECOMMENDER_APPROVER"], "RA.EMPLOYEE_ID=TR.EMPLOYEE_ID", ['RECOMMENDER_ID' => 'RECOMMEND_BY', 'APPROVER_ID' => 'APPROVED_BY'], "left")
            ->join(['RECM' => "HRIS_EMPLOYEES"], "RECM.EMPLOYEE_ID=RA.RECOMMEND_BY", ['RECOMMENDER_NAME' => new Expression("INITCAP(RECM.FULL_NAME)")], "left")
            ->join(['APRV' => "HRIS_EMPLOYEES"], "APRV.EMPLOYEE_ID=RA.APPROVED_BY", ['APPROVER_NAME' => new Expression("INITCAP(APRV.FULL_NAME)")], "left");
        $select->where([
            "E.EMPLOYEE_ID=" . $employeeId
        ]);
        $select->order("TR.REQUESTED_DATE DESC");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return iterator_to_array($result, false);
    }
}
