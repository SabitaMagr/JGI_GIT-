<?php

namespace Application\Repository;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class HrisRepository {

    protected $adapter;
    protected $tableGateway;

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        $this->adapter = $adapter;
        if ($tableName !== null) {
            $this->tableGateway = new TableGateway($tableName, $adapter);
        }
    }

    protected function rawQuery($sql): array {
        $statement = $this->adapter->query($sql);
        $iterator = $statement->execute();
        return iterator_to_array($iterator, false);
    }

    protected function checkIfTableExists($tableName): bool {
        $sql = "SELECT * FROM USER_TABLES WHERE TABLE_NAME ='{$tableName}'";
        $statement = $this->adapter->query($sql);
        $iterator = $statement->execute();
        return $iterator->count() > 0;
    }

}
