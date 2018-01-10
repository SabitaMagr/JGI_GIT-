<?php

namespace System\Repository;

use Application\Repository\HrisRepository;
use Zend\Db\Adapter\AdapterInterface;

class SynergyRepository extends HrisRepository {

    private $linkedWithSynergy = false;

    const FORM_SETUP = "FORM_SETUP";
    const FA_CHART_OF_ACCOUNTS_SETUP = "FA_CHART_OF_ACCOUNTS_SETUP";

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        parent::__construct($adapter, $tableName);
        $this->linkedWithSynergy = $this->checkIfTableExists(self::FORM_SETUP);
    }

    public function getFormList() {
        if (!$this->linkedWithSynergy) {
            return [];
        }
        $sql = "SELECT * FROM FORM_SETUP WHERE GROUP_SKU_FLAG = 'I' AND COMPANY_CODE = '07' ORDER BY FORM_EDESC";
        return $this->rawQuery($sql);
    }

    public function getAccountList() {
        if (!$this->linkedWithSynergy) {
            return [];
        }
        $sql = "SELECT * FROM FA_CHART_OF_ACCOUNTS_SETUP ORDER BY ACC_EDESC";
        return $this->rawQuery($sql);
    }

}
