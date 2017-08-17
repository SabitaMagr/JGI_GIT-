<?php

namespace Payroll\Controller;

use Application\Factory\HrLogger;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\RepositoryInterface;
use Exception;
use Payroll\Model\FlatValue as FlatValueModel;
use Payroll\Model\MonthlyValue as MonthlyValueModel;
use Payroll\Model\PayEmployeeSetup;
use Payroll\Model\Rules;
use Payroll\Model\RulesDetail;
use Payroll\Repository\FlatValueDetailRepo;
use Payroll\Repository\MonthlyValueDetailRepo;
use Payroll\Repository\PayEmployeeRepo;
use Payroll\Repository\RulesDetailRepo;
use Payroll\Repository\RulesRepository;

class PayrollGenerator {

    private $adapter;
    private $logger;
    private $flatValueDetRepo;
    private $monthlyValueDetRepo;
    private $payEmployeeRepo;
    private $ruleDetailRepo;
    private $ruleRepo;
    private $employeeId;
    private $monthlyValues;
    private $flatValues;
    private $calculatedValue = 0;
    private $ruleDetailList = [];
    private $monthId = 0;

    const VARIABLES = [
        "BASIC_SALARY",
        "MONTH_DAYS",
        "PRESENT_DAYS",
        "ABSENT_DAYS",
        "PAID_LEAVES",
        "UNPAID_LEAVES",
        "DAY_OFFS",
        "HOLIDAYS",
        "DAYS_FROM_JOIN_DATE",
        "DAYS_FROM_PERMANENT_DATE",
        "IS_MALE",
        "IS_FEMALE",
        "IS_MARRIED",
        "IS_PERMANENT",
        "IS_PROBATION",
        "IS_CONTRACT",
        "IS_TEMPORARY"
    ];
    const SYSTEM_RULE = [
        "CUR_MTH_ID",
        "MTH_RESULT",
        "YR_RESULT",
        "CUR_MTH_LAST_DAY",
    ];

    public function __construct($adapter, int $monthId) {
        $this->adapter = $adapter;
        $this->logger = HrLogger::getInstance();

        $this->monthId = $monthId;
        $this->flatValueDetRepo = new FlatValueDetailRepo($adapter);
        $this->monthlyValueDetRepo = new MonthlyValueDetailRepo($adapter);
        $this->payEmployeeRepo = new PayEmployeeRepo($adapter);
        $this->ruleDetailRepo = new RulesDetailRepo($adapter);
        $this->ruleRepo = new RulesRepository($adapter);

        $this->monthlyValues = EntityHelper::getTableKVListWithSortOption($this->adapter, MonthlyValueModel::TABLE_NAME, MonthlyValueModel::MTH_ID, [MonthlyValueModel::MTH_EDESC], null, null, null, null, false, true);
        $this->flatValues = EntityHelper::getTableKVListWithSortOption($this->adapter, FlatValueModel::TABLE_NAME, FlatValueModel::FLAT_ID, [FlatValueModel::FLAT_EDESC], null, null, null, null, false, true);

        $this->sanitizeStringArray($this->monthlyValues);
        $this->sanitizeStringArray($this->flatValues);
    }

    private function getPositionId($id) {
        return EntityHelper::getTableKVList($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["POSITION_ID"], ["EMPLOYEE_ID" => $id], null)[$id];
    }

    public function generate($id) {
        $this->employeeId = $id;
        $payList = $this->payEmployeeRepo->fetchByEmployeeId($this->employeeId);

        $ruleValueKV = [];
        $counter = 0;
        foreach ($payList as $ruleObj) {
            $ruleId = $ruleObj[PayEmployeeSetup::PAY_ID];
            $ruleDetail = $this->ruleDetailRepo->fetchById($ruleId)->getArrayCopy();
            $rule = $ruleDetail[RulesDetail::MNENONIC_NAME];
            $operationType = $ruleObj[Rules::PAY_TYPE_FLAG];

            $ruleRepo = new RulesRepository($this->adapter);
            $refRules = $ruleRepo->fetchReferencingRules($ruleId);
            $refRules = Helper::extractDbData($refRules);

            foreach ($this->monthlyValues as $monthlyKey => $monthlyValue) {
                $rule = $this->convertConstantToValue($rule, $monthlyKey, $monthlyValue, $this->monthlyValueDetRepo);
            }

            foreach ($this->flatValues as $flatKey => $flatValue) {
                $rule = $this->convertConstantToValue($rule, $flatKey, $flatValue, $this->flatValueDetRepo);
            }

            foreach (self::VARIABLES as $variable) {
                $rule = $this->convertVariableToValue($rule, $variable);
            }

            foreach (self::SYSTEM_RULE as $systemRule) {
                $rule = $this->convertSystemRuleToValue($rule, $systemRule);
            }
            $rule = $this->convertReferencingRuleToValue($rule, $refRules);

            $this->logger->info("payroll", ['employeeId' => $this->employeeId, 'ruleId' => $ruleId]);
            $ruleValue = eval("return " . $rule . " ;");

            array_push($this->ruleDetailList, ["ruleValue" => $ruleValue, "rule" => $ruleObj, "ruleDetail" => $ruleDetail]);

            if ($operationType == 'A') {
                $this->calculatedValue = $this->calculatedValue + $ruleValue;
            } else if ($operationType == 'D') {
                $this->calculatedValue = $this->calculatedValue - $ruleValue;
            }
            $ruleValueKV[$ruleId] = $ruleValue;
            $counter++;
        }

        return ["ruleValueKV" => $ruleValueKV, "calculatedValue" => $this->calculatedValue];
    }

    private function sanitizeStringArray(array &$stringArray) {
        foreach ($stringArray as &$string) {
            $string = str_replace(" ", "_", $string);
            $string = strtoupper($string);
        }
    }

    private function convertConstantToValue($rule, $key, $constant, RepositoryInterface $repository) {
        if (strpos($rule, $this->wrapWithLargeBracket($constant)) !== false) {
            return str_replace($this->wrapWithLargeBracket($constant), $this->generateValue($key, $repository), $rule);
        } else {
            return $rule;
        }
    }

    private function generateValue($constant, RepositoryInterface $repository) {
        if ($repository instanceof MonthlyValueDetailRepo) {
            $monthlyValTmp = $repository->fetchById(['employeeId' => $this->employeeId, 'monthId' => $this->monthId, 'mthId' => $constant]);
            $monthlyValTmp = (!isset($monthlyValTmp)) ? 0 : $monthlyValTmp;
            return $monthlyValTmp;
        } else if ($repository instanceof FlatValueDetailRepo) {
            $flatValTmp = $repository->fetchById(['employeeId' => $this->employeeId, 'monthId' => $this->monthId, 'flatId' => $constant]);
            $flatValTmp = (isset($flatValTmp)) ? $flatValTmp : 0;
            return $flatValTmp;
        }
    }

    private function convertVariableToValue($rule, $variable) {
        if (strpos($rule, $this->wrapWithLargeBracket($variable)) !== false) {
            $variableProcessor = new VariableProcessor($this->adapter, $this->employeeId, $this->monthId);
            $processedVariable = $variableProcessor->processVariable($variable);
            if (is_string($processedVariable)) {
                return str_replace($this->wrapWithLargeBracket($variable), "'" . $processedVariable . "'", $rule);
            } else {
                return str_replace($this->wrapWithLargeBracket($variable), $processedVariable, $rule);
            }
        } else {
            return $rule;
        }
    }

    private function convertSystemRuleToValue($rule, $variable) {
        if (strpos($rule, $this->wrapWithLargeBracket($variable)) !== false) {
            $systemRuleProcessor = new SystemRuleProcessor($this->adapter, $this->employeeId, $this->calculatedValue, $this->ruleDetailList, $this->monthId);
//            return str_replace($variable, $systemRuleProcessor->processSystemRule($variable), $rule);
            $processedSystemRule = $systemRuleProcessor->processSystemRule($variable);
            if (is_string($processedSystemRule)) {
                return str_replace($this->wrapWithLargeBracket($variable), "'" . $processedSystemRule . "'", $rule);
            } else {
                return str_replace($this->wrapWithLargeBracket($variable), $processedSystemRule, $rule);
            }
        } else {
            return $rule;
        }
    }

    private function convertReferencingRuleToValue($rule, $refRules) {
        foreach ($refRules as $refRule) {
            $payEdesc = $refRule['PAY_EDESC'];
            $payEdesc = str_replace(" ", "_", $payEdesc);
            $payEdesc = strtoupper($payEdesc);
            if (strpos($rule, $this->wrapWithSmallBracket($payEdesc)) !== false) {
                $processedRefRules = 0;
                foreach ($this->ruleDetailList as $ruleDetail) {
                    if ($ruleDetail['rule']['PAY_ID'] == $refRule['PAY_ID']) {
                        if (!isset($ruleDetail['ruleValue'])) {
                            print "<pre>";
                            print 'not set';
                            exit;
                        }

                        $processedRefRules = $ruleDetail['ruleValue'];
                    }
                }
                if (is_string($processedRefRules)) {
                    $rule = str_replace($payEdesc, "'" . $processedRefRules . "'", $rule);
                } else {
                    $rule = str_replace($payEdesc, $processedRefRules, $rule);
                }
            }
        }
        return $rule;
    }

    private function wrapWithLargeBracket($input) {
        return "[" . $input . "]";
    }

    private function unwrapWithLargeBracket($input) {
        $temp = str_replace("[", "", $input);
        $temp = str_replace("]", "", $temp);
        return $temp;
    }

    private function wrapWithSmallBracket($input) {
        return "(" . $input . ")";
    }

    private function unwrapWithSmallBracket($input) {
        $temp = str_replace("(", "", $input);
        $temp = str_replace(")", "", $temp);
        return $temp;
    }

}
