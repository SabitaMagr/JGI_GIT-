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
    }

    public function editDesignation(Designation $designation)
    {
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
    }
}