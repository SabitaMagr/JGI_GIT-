<?php

namespace Setup\Model;

use Zend\Db\TableGateway\TableGateway;

class DesignationRepository implements DesignationRepositoryInterface
{
    private $tableGateway;
    public function __construct(TableGateway $designationTableGateway){
        $this->tableGateway=$designationTableGateway;
    }

    public function addDesignation(Designation $designation)
    {
        $this->tableGateway->insert($designation->getArrayCopy());
    }

    public function editDesignation(Designation $designation,$id)
    {
        $this->tableGateway->update($designation->getArrayCopy(),["designationCode"=>$id]);
    }

    public function deleteDesignation(Designation $designation)
    {
    }

    public function fetchAll()
    {
       return $this->tableGateway->select();
    }

    public function fetchById($id)
    {
       $rowset= $this->tableGateway->select(["designationCode"=>$id]);
        return $rowset->current();
    }
}