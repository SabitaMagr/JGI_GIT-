<?php
namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Model\Loan;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class LoanRepository implements RepositoryInterface{
	private $tableGateway;

	public function __construct(AdapterInterface $adapter){
		$this->tableGateway = new TableGateway(Loan::TABLE_NAME,$adapter);
	}
	public function add(Model $model){
		$this->tableGateway->insert($model->getArrayCopyForDB());
	}
	public function edit(Model $model,$id){
		$array = $model->getArrayCopyForDB();
		unset($array[Loan::LOAN_ID]);
		unset($array[Loan::CREATED_DATE]);
		$this->tableGateway->update($array,[Loan::LOAN_ID=>$id]);
	}
	public function delete($id){
        $this->tableGateway->update([Loan::STATUS=>'D'],[Loan::LOAN_ID => $id]);
	}
	public function fetchAll(){
		return $this->tableGateway->select();
	}
	public function fetchActiveRecord()
    {
        $rowset= $this->tableGateway->select(function(Select $select){
                $select->where([Loan::STATUS=>'E']);
                $select->order(Loan::LOAN_NAME." ASC");
            });
        $result = [];
        $i=1;
        foreach($rowset as $row){
            array_push($result, [
                'SN'=>$i,
                'LOAN_ID'=>$row['LOAN_ID'],
                'LOAN_CODE'=>$row['LOAN_CODE'],
                'LOAN_NAME'=>$row['LOAN_NAME'],
                'MIN_AMOUNT'=>$row['MIN_AMOUNT'],
                'MAX_AMOUNT'=>$row['MAX_AMOUNT'],
                'INTEREST_RATE'=>$row['INTEREST_RATE'],
                'REPAYMENT_AMOUNT'=>$row['REPAYMENT_AMOUNT'],
                'REPAYMENT_PERIOD'=>$row['REPAYMENT_PERIOD'],
                'REMARKS'=>$row['REMARKS']
            ]);
            $i+=1;
        }
        return $result;
    }
	public function fetchById($id){
		$row = $this->tableGateway->select([Loan::LOAN_ID=>$id]);
		return $row->current();
	}
}