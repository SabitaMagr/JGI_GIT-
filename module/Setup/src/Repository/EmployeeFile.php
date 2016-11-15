<?php
namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class EmployeeFile implements RepositoryInterface
{
    private $tableGateway;
    private $fileTypeTableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway=new TableGateway('HR_EMPLOYEE_FILE',$adapter);
        $this->fileTypeTableGateway = new TableGateway('HR_FILE_TYPE',$adapter);
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array = $model->getArrayCopyForDB();
        unset($array['CREATED_DT']);
        unset($array['FILE_CODE']);
        $this->tableGateway->update($array,["FILE_CODE"=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }


    public function fetchById($id)
    {
//        $rowset= $this->tableGateway->select(['EMPLOYEE_ID'=>$id]);
        $rowset= $this->tableGateway->select(function (Select $select) use ($id) {
            $select->where(['EMPLOYEE_ID'=>$id]);
            $select->order('CREATED_DT DESC')->limit(1);
        });
        return $rowset->current();
    }

    public function delete($id)
    {
        $this->tableGateway->delete(['FILE_CODE'=>$id]);
    }
    public function fetchAllFileType(){
        return $this->fileTypeTableGateway->select(['STATUS'=>'E']);
    }
}