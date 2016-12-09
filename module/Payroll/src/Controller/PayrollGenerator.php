<?php

namespace Payroll\Controller;

use Application\Helper\EntityHelper;
use Application\Repository\RepositoryInterface;
use Payroll\Model\FlatValueDetail;
use Payroll\Model\MonthlyValueDetail;
use Payroll\Model\PayPositionSetup;
use Payroll\Model\Rules;
use Payroll\Model\RulesDetail;
use Payroll\Repository\FlatValueDetailRepo;
use Payroll\Repository\MonthlyValueDetailRepo;
use Payroll\Model\FlatValue as FlatValueModel;
use Payroll\Model\MonthlyValue as MonthlyValueModel;
use Payroll\Repository\PayPositionRepo;
use Payroll\Repository\RulesDetailRepo;
use Payroll\Repository\RulesRepository;
use Setup\Entity\HrEmployees;

class PayrollGenerator {

    private $adapter;
    private $flatValueDetRepo;
    private $monthlyValueDetRepo;
    private $payPositionRepo;
    private $ruleDetailRepo;
    private $ruleRepo;
    private $employeeId;
    private $monthlyValues;
    private $flatValues;
    private $calculatedValue = 0;
    private $ruleDetailList = [];

    const VARIABLES = [
        "BASIC_PER_MONTH",
        "NO_OF_DAYS_IN_CURRENT_MONTH",
        "NO_OF_DAYS_ABSENT",
        "NO_OF_DAYS_WORKED",
        "NO_OF_PAID_LEAVES",
        "NO_OF_HOLIDAYS",
        "TOTAL_DAYS_TO_PAY",
        "GENDER",
        "EMP_TYPE",
        "MARITUAL_STATUS",
        "TOTAL_DAYS_FROM_JOIN_DATE",
        "SERVICE_TYPE"
    ];
    const SYSTEM_RULE = [
        "MONTH",
        "RESULT",
        "YEARLY_VALUE"
    ];

    public function __construct($adapter) {
        $this->adapter = $adapter;
        $this->flatValueDetRepo = new FlatValueDetailRepo($adapter);
        $this->monthlyValueDetRepo = new MonthlyValueDetailRepo($adapter);
        $this->payPositionRepo = new PayPositionRepo($adapter);
        $this->ruleDetailRepo = new RulesDetailRepo($adapter);
        $this->ruleRepo = new RulesRepository($adapter);

        $this->monthlyValues = EntityHelper::getTableKVList($this->adapter, MonthlyValueModel::TABLE_NAME, MonthlyValueModel::MTH_ID, [MonthlyValueModel::MTH_EDESC]);
        $this->flatValues = EntityHelper::getTableKVList($this->adapter, FlatValueModel::TABLE_NAME, FlatValueModel::FLAT_ID, [FlatValueModel::FLAT_EDESC]);

        $this->sanitizeStringArray($this->monthlyValues);
        $this->sanitizeStringArray($this->flatValues);
    }

    private function getPositionId($id) {
        return EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["POSITION_ID"], ["EMPLOYEE_ID" => $id], null)[$id];
    }

    public function generate($id) {
        $this->employeeId = $id;

        $positionId = $this->getPositionId($id);
        if ($positionId == null) {
            $payPositionList = [];
        } else {
            $payPositionList = $this->payPositionRepo->test($positionId);
        }
        $payList = [];

        foreach ($payPositionList as $payPosition) {
            array_push($payList, $payPosition);
        }
        $ruleValueKV = [];
//        print "<pre>";
        $counter = 0;
        foreach ($payList as $ruleObj) {
            $ruleId = $ruleObj[PayPositionSetup::PAY_ID];
            $ruleDetail = $this->ruleDetailRepo->fetchById($ruleId)->getArrayCopy();
            $rule = $ruleDetail[RulesDetail::MNENONIC_NAME];
            $operationType = $ruleObj[Rules::PAY_TYPE_FLAG];

            foreach ($this->monthlyValues as $key => $monthlyValue) {
                $rule = $this->convertConstantToValue($rule, $key, $monthlyValue, $this->monthlyValueDetRepo);
            }

            foreach ($this->flatValues as $key => $flatValue) {
                $rule = $this->convertConstantToValue($rule, $key, $flatValue, $this->flatValueDetRepo);
            }

            foreach (self::VARIABLES as $variable) {
                $rule = $this->convertVariableToValue($rule, $variable);
            }

            foreach (self::SYSTEM_RULE as $systemRule) {
                $rule = $this->convertSystemRuleToValue($rule, $systemRule);
            }
//            if ($counter == 1) {
//                print "<pre>";
//                print_r($rule);
//                exit;
//            }
//            print($rule);
//            try {
            $ruleValue = eval($rule);
//            } catch (\Exception $e) {
//                print "<pre>";
//                print($rule);
//                exit;
//            }
            array_push($this->ruleDetailList, ["ruleValue" => $ruleValue, "rule" => $ruleObj, "ruleDetail" => $ruleDetail]);

            if ($operationType == 'A') {
                $this->calculatedValue = $this->calculatedValue + $ruleValue;
            } else if ($operationType == 'D') {
                $this->calculatedValue = $this->calculatedValue - $ruleValue;
            }
            $ruleValueKV[$ruleId] = $ruleValue;
            $counter++;
        }
//        exit;

        return ["ruleValueKV" => $ruleValueKV, "calculatedValue" => $this->calculatedValue];
    }

    private function sanitizeStringArray(array &$stringArray) {
        foreach ($stringArray as &$string) {
            $string = str_replace(" ", "_", $string);
            $string = strtoupper($string);
        }
    }

    private function convertConstantToValue($rule, $key, $constant, RepositoryInterface $repository) {
        if (strpos($rule, $constant) !== false) {
            return str_replace($constant, $this->generateValue($key, $repository), $rule);
        } else {
            return $rule;
        }
    }

    private function generateValue($constant, RepositoryInterface $repository) {
        if ($repository instanceof MonthlyValueDetailRepo) {
            return $repository->fetchById([$this->employeeId, $constant])[MonthlyValueDetail::MTH_VALUE];
        } else if ($repository instanceof FlatValueDetailRepo) {
            return $repository->fetchById([$this->employeeId, $constant])[FlatValueDetail::FLAT_VALUE];
        }
    }

    private function convertVariableToValue($rule, $variable) {
        if (strpos($rule, $variable) !== false) {
            $variableProcessor = new VariableProcessor($this->adapter, $this->employeeId);
//            return str_replace($variable, $variableProcessor->processVariable($variable), $rule);
            $processedVariable = $variableProcessor->processVariable($variable);
            if (is_string($processedVariable)) {
                return str_replace($variable, "'" . $processedVariable . "'", $rule);
            } else {
                return str_replace($variable, $processedVariable, $rule);
            }
        } else {
            return $rule;
        }
    }

    private function convertSystemRuleToValue($rule, $variable) {
        if (strpos($rule, $variable) !== false) {
            $systemRuleProcessor = new SystemRuleProcessor($this->adapter, $this->employeeId, $this->calculatedValue, $this->ruleDetailList);
//            return str_replace($variable, $systemRuleProcessor->processSystemRule($variable), $rule);
            $processedSystemRule = $systemRuleProcessor->processSystemRule($variable);
            if (is_string($processedSystemRule)) {
                return str_replace($variable, "'" . $processedSystemRule . "'", $rule);
            } else {
                return str_replace($variable, $processedSystemRule, $rule);
            }
        } else {
            return $rule;
        }
    }

}
