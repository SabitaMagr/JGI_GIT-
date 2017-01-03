<?php

namespace Payroll\Controller;

use Application\Helper\Helper;
use Application\Repository\MonthRepository;
use Setup\Repository\EmployeeRepository;

class SystemRuleProcessor {

    private $adapter;
    private $employeeId;
    private $employeeRepo;
    private $calculatedValue;
    private $ruleDetailList;
    private $monthId;

    public function __construct($adapter, $employeeId, $calculatedValue, $ruleDetailList, int $monthId) {
        $this->adapter = $adapter;
        $this->employeeId = $employeeId;
        $this->employeeRepo = new EmployeeRepository($adapter);
        $this->calculatedValue = $calculatedValue;
        $this->ruleDetailList = $ruleDetailList;
        $this->monthId = $monthId;
    }

    public function processSystemRule($systemRule) {
        $processedValue = "";
        switch ($systemRule) {
//            "CUR_MTH_VAL"
            case PayrollGenerator::SYSTEM_RULE[0]:
//                $currentMonth = date('m');
//                $processedValue = $currentMonth;
                $processedValue = $this->monthId;
                break;
//            "MTH_RESULT"
            case PayrollGenerator::SYSTEM_RULE[1]:
                $processedValue = $this->calculatedValue;
                break;
//            "YR_RESULT"
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
//                "CUR_MTH_START_VAL"
            case PayrollGenerator::SYSTEM_RULE[3]:
                $monthRepo = new MonthRepository($this->adapter);
                $month = $monthRepo->fetchByMonthId($this->monthId);
                $dateObj = \DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $month['FROM_DATE']);
                $processedValue = $dateObj->format("d");
                break;
//            "CUR_MTH_END_VAL"
            case PayrollGenerator::SYSTEM_RULE[4]:
                $monthRepo = new MonthRepository($this->adapter);
                $month = $monthRepo->fetchByMonthId($this->monthId);
                $dateObjFrom = \DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $month['FROM_DATE']);
                $dateObjTo = \DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $month['TO_DATE']);
                $interval = $dateObjFrom->diff($dateObjTo);
//                $processedValue = $dateObj->format("d");
                $processedValue = $interval->d;
                break;
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
