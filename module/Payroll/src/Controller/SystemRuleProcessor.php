<?php

namespace Payroll\Controller;

use Application\Model\Months;
use Application\Repository\MonthRepository;
use Payroll\Repository\SalarySheetDetailRepo;
use Setup\Repository\EmployeeRepository;

class SystemRuleProcessor {

    private $adapter;
    private $employeeId;
    private $employeeRepo;
    private $ruleDetailList;
    private $monthId;
    private $month;
    private $multiplicationFactor;
    private $prevSummedSSD;
    private $ruleId;

    public function __construct($adapter, $employeeId, $ruleDetailList, int $monthId, int $ruleId) {
        $this->adapter = $adapter;
        $this->employeeId = $employeeId;
        $this->employeeRepo = new EmployeeRepository($adapter);
        $monthRepo = new MonthRepository($adapter);
        $this->ruleDetailList = $ruleDetailList;
        $this->monthId = $monthId;
        $this->month = new Months();
        $this->month->exchangeArrayFromDB((array) $monthRepo->fetchByMonthId($monthId));
        $this->multiplicationFactor = 13 - $this->month->fiscalYearMonthNo;

        $ssdRepo = new SalarySheetDetailRepo($adapter);
        $prevSummedRaw = $ssdRepo->fetchPrevSumPayValue($employeeId, $this->month->fiscalYearId, $this->month->fiscalYearMonthNo);
        $this->prevSummedSSD = $this->listValueToKV($prevSummedRaw, "PAY_ID", "PREV_SUM_VAL");

        $this->ruleId = $ruleId;
    }

    private function listValueToKV($list, $key, $value) {
        $output = [];
        foreach ($list as $item) {
            $output[$item[$key]] = $item[$value];
        }
        return $output;
    }

    public function processSystemRule($systemRule) {
        $processedValue = "";
        switch ($systemRule) {
//            "TOTAL_ANNUAL_AMOUNT"
            case PayrollGenerator::SYSTEM_RULE[0]:
                $calculatedValue = 0;
                foreach ($this->ruleDetailList as $ruleDetail) {
                    if (in_array($ruleDetail['rule']['PAY_TYPE_FLAG'], ['A', 'D'])) {
                        $ruleValue = (($this->multiplicationFactor == 12) ? 0 : $this->prevSummedSSD[$ruleDetail['rule']['PAY_ID']]) + ($ruleDetail['ruleValue'] * (($ruleDetail['rule']['IS_MONTHLY'] == 'Y') ? 1 : $this->multiplicationFactor));
                    }
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
//       TOTAL_AMOUNT
            case PayrollGenerator::SYSTEM_RULE[1]:
                $calculatedValue = 0;
                foreach ($this->ruleDetailList as $ruleDetail) {
                    if (in_array($ruleDetail['rule']['PAY_TYPE_FLAG'], ['A', 'D'])) {
                        $ruleValue = $ruleDetail['ruleValue'];
                    }
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
//            SELF_PREV_TOTAL
            case PayrollGenerator::SYSTEM_RULE[2]:
                $processedValue = isset($this->prevSummedSSD[$this->ruleId]) ? $this->prevSummedSSD[$this->ruleId] : 0;
                break;
            case PayrollGenerator::SYSTEM_RULE[3]:
                $processedValue = $this->multiplicationFactor;
                break;
        }
        return $processedValue;
    }

}
