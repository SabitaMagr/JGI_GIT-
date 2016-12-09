<?php

namespace Payroll\Controller;

use Setup\Repository\EmployeeRepository;

class SystemRuleProcessor {

    private $adapter;
    private $employeeId;
    private $employeeRepo;
    private $calculatedValue;
    private $ruleDetailList;

    public function __construct($adapter, $employeeId, $calculatedValue, $ruleDetailList) {
        $this->adapter = $adapter;
        $this->employeeId = $employeeId;
        $this->employeeRepo = new EmployeeRepository($adapter);
        $this->calculatedValue = $calculatedValue;
        $this->ruleDetailList = $ruleDetailList;
    }

    public function processSystemRule($systemRule) {
        $processedValue = "";
        switch ($systemRule) {
            case PayrollGenerator::SYSTEM_RULE[0]:
                $currentMonth = date('m');
                $processedValue = $currentMonth;
                break;
            case PayrollGenerator::SYSTEM_RULE[1]:
                $processedValue = $this->calculatedValue;
                break;
            case PayrollGenerator::SYSTEM_RULE[2]:
                $calculatedValue = 0;
                foreach ($this->ruleDetailList as $ruleDetail) {
                    $ruleValue = ($ruleDetail['ruleDetail']['IS_MONTHLY'] == "N") ? $ruleDetail['ruleValue'] * 12 : $ruleDetail['ruleValue'];
                    if ($ruleDetail['rule']['PAY_TYPE_FLAG'] == 'A') {
                        $calculatedValue = $calculatedValue + $ruleValue;
                    } else {
                        $calculatedValue = $calculatedValue - $ruleValue;
                    }
                }
                $processedValue = $calculatedValue;
                break;
//            case PayrollGenerator::SYSTEM_RULE[3]:
//                break;
//            case PayrollGenerator::SYSTEM_RULE[4]:
//                break;
//            case PayrollGenerator::SYSTEM_RULE[5]:
//                break;
//            case PayrollGenerator::SYSTEM_RULE[6]:
//                break;
//            case PayrollGenerator::SYSTEM_RULE[7]:
//                break;
//            case PayrollGenerator::SYSTEM_RULE[8]:
//                break;
//            case PayrollGenerator::SYSTEM_RULE[9]:
//                break;
        }
        return $processedValue;
    }

}
