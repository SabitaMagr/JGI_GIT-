<?php

namespace SelfService\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;

class PayslipPreviousRepository extends HrisRepository {

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, $tableName);
    }

    public function getPeriodList($companyCode) {
        $sql = "
                SELECT PERIOD_DT_CODE AS mcode,
                  DT_EDESC            AS mname
                FROM HR_PERIOD_DETAIL
                WHERE COMPANY_CODE='{$companyCode}'
                ORDER BY to_number(PERIOD_DT_CODE) ;";
        return $this->rawQuery($sql);
    }

}
