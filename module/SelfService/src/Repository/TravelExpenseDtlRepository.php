<?php
namespace SelfService\Repository;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use SelfService\Model\TravelExpenseDetail;
use SelfService\Model\TravelRequest;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Authentication\AuthenticationService;
use Application\Helper\Helper;

class TravelExpenseDtlRepository implements RepositoryInterface{
    private $tableGateway;
    private $adapter;
    private $loggedInEmployee;
    
    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(TravelExpenseDetail::TABLE_NAME,$adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->loggedInEmployee = $auth->getStorage()->read()['employee_id'];
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $modifiedDt = Helper::getcurrentExpressionDate();
        $employeeID = $this->loggedInEmployee;
        $this->tableGateway->update([TravelExpenseDetail::STATUS=>'D', TravelExpenseDetail::MODIFIED_BY=>$employeeID, TravelExpenseDetail::MODIFIED_DATE=>$modifiedDt],[TravelExpenseDetail::ID=>$id]);
    }

    public function edit(Model $model, $id) {
        $tempData = $model->getArrayCopyForDB();
        $this->tableGateway->update($model->getArrayCopyForDB(),[TravelExpenseDetail::ID=>$id]);
    }

    public function fetchAll() {
        
    }

    public function fetchByTravelId($travelId){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new TravelExpenseDetail(), ['departureDate', 'destinationDate'], ['departureTime', 'destinationTime'],'TR'), false);
        $select->from(['TR' => TravelExpenseDetail::TABLE_NAME])
                ->join(['T' => TravelRequest::TABLE_NAME], "T." . TravelRequest::TRAVEL_ID . "=TR." . TravelExpenseDetail::TRAVEL_ID, [TravelRequest::TRAVEL_CODE]);

        $select->where([
            "TR.TRAVEL_ID=" . $travelId,
            "TR.STATUS='E'"
        ]);
        $select->order("TR.ID");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }
    public function fetchById($id) {
        
    }

}
