<?php
namespace Payroll\Repository;

use Application\Repository\HrisRepository;
use Payroll\Model\SalarySheetEmpDetail;
use Zend\Db\Adapter\AdapterInterface;

class SalSheEmpDetRepo extends HrisRepository {

    public function __construct(AdapterInterface $adapter) {
        parent::__construct($adapter, SalarySheetEmpDetail::TABLE_NAME);
    }

    public function fetchOneBy($by) {
        return $this->tableGateway->select($by)->current();
    }
}
