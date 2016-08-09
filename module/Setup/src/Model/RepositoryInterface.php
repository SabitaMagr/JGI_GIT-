<?php

namespace Setup\Model;


use Zend\Db\Adapter\AdapterInterface;

interface  RepositoryInterface{

    public function __construct(AdapterInterface $adapter);

    public function add(ModelInterface $model);
    public function edit(ModelInterface $model,$id,$modifiedDt);
    public function  fetchAll();
    public function fetchById($id);
    public function delete($id);
}