<?php

namespace Payroll\Controller;

use Setup\Repository\EmployeeRepository;

class SystemRuleProcessor {

    private $adapter;
    private $employeeId;
    private $employeeRepo;
    private $ruleDetailList;
    private $monthId;

    public function __construct($adapter, $employeeId, $ruleDetailList, int $monthId) {
        $this->adapter = $adapter;
        $this->employeeId = $employeeId;
        $this->employeeRepo = new EmployeeRepository($adapter);
        $this->ruleDetailList = $ruleDetailList;
        $this->monthId = $monthId;
    }

    public function processSystemRule($systemRule) {
        $processedValue = "";
        switch ($systemRule) {
//            "YR_RESULT"
            case PayrollGenerator::SYSTEM_RULE[0]:
                $calculatedValue = 0;
                foreach ($this->ruleDetailList as $ruleDetail) {
                    $ruleValue = $ruleDetail['rule']['IS_MONTHLY'] == 'Y' ? $ruleDetail['ruleValue'] : $ruleDetail['ruleValue'] * 12;
                    switch ($ruleDetail['rule']['PAY_TYPE_FLAG']) {
                        case "A":
                            $calculatedValue = $calculatedValue + $ruleValue;
                            break;
                        case "D":
                            $calculatedValue = $calculatedValue - $ruleValue;
                            break;
                    }
                }
                $processedValue = $calculatedValue;
                break;
        }
        return $processedValue;
    }

}
