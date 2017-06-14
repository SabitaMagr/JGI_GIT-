<?php

namespace Overtime\Repository;

use Overtime\Model\CompulsoryOvertime;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class OvertimeAutomationRepository {

    private $adapter;
    private $tableGateway;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(CompulsoryOvertime::TABLE_NAME, $adapter);
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

}
