<?php
namespace Setup\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Company;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class CompanyRepository implements RepositoryInterface
{
    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter=$adapter;
        $this->tableGateway = new TableGateway(Company::TABLE_NAME,$adapter);

    }

    public function add(Model $model)
    {
//        print "<pre>";
//        print_r($model);
//        exit;
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model,$id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array,[Company::COMPANY_ID=>$id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select([Company::STATUS=>'E']);
    }

    public function fetchById($id)
    {
        $rowset= $this->tableGateway->select([Company::COMPANY_ID=>$id,Company::STATUS=>'E']);
        return $rowset->current();
    }

    public function delete($id)
    {
        $this->tableGateway->delete([Company::COMPANY_ID=>$id]);
    }
}