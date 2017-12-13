<?php

namespace Payroll\Controller;

use Application\Repository\RepositoryInterface;
use Exception;
use Payroll\Controller\SystemRuleProcessor;
use Payroll\Controller\VariableProcessor;
use Payroll\Model\Rules;
use Payroll\Repository\FlatValueDetailRepo;
use Payroll\Repository\FlatValueRepository;
use Payroll\Repository\MonthlyValueDetailRepo;
use Payroll\Repository\MonthlyValueRepository;
use Payroll\Repository\RulesRepository;

class PayrollGenerator {

    private $adapter;
    private $flatValueDetRepo;
    private $monthlyValueDetRepo;
    private $ruleRepo;
    private $employeeId;
    private $monthId = 0;
    private $formattedFlatValueList;
    private $formattedMonthlyvalueList;
    private $formattedVariableList;
    private $formattedSystemRuleList;
    private $formattedReferencingRuleList;
    private $calculatedValue = 0;
    private $ruleDetailList = [];

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

    public function __construct($adapter) {
        $this->adapter = $adapter;
        $this->flatValueDetRepo = new FlatValueDetailRepo($adapter);
        $this->monthlyValueDetRepo = new MonthlyValueDetailRepo($adapter);
        $this->ruleRepo = new RulesRepository($adapter);
        $monthlyValueList = $this->getMonthlyValues();
        $flatValuesList = $this->getFlatValues();

        $this->formattedMonthlyvalueList = [];
        foreach ($monthlyValueList as $monthlyValue) {
            $this->formattedMonthlyvalueList[$monthlyValue['MTH_ID']] = $this->sanitizeString($monthlyValue['MTH_EDESC']);
        }
        $this->formattedFlatValueList = [];
        foreach ($flatValuesList as $flatValue) {
            $this->formattedFlatValueList[$flatValue['FLAT_ID']] = $this->sanitizeString($flatValue['FLAT_EDESC']);
        }

        $this->formattedVariableList = [];
        foreach (self::VARIABLES as $variable) {
            array_push($this->formattedVariableList, $variable);
        }

        $this->formattedSystemRuleList = [];
        foreach (self::SYSTEM_RULE as $systemRule) {
            array_push($this->formattedSystemRuleList, $systemRule);
        }
    }

    public function generate($employeeId, $monthId) {
        $this->employeeId = $employeeId;
        $this->monthId = $monthId;
        $payList = $this->ruleRepo->fetchAll();

        $ruleValueMap = [];
        $counter = 0;
        foreach ($payList as $ruleDetail) {
            $ruleId = $ruleDetail[Rules::PAY_ID];
            $formula = $ruleDetail[Rules::FORMULA];
            $operationType = $ruleDetail[Rules::PAY_TYPE_FLAG];

            $refRules = $this->ruleRepo->fetchReferencingRules($ruleId);

            foreach ($this->monthlyValues as $monthlyKey => $monthlyValue) {
                $formula = $this->convertConstantToValue($formula, $monthlyKey, $monthlyValue, $this->monthlyValueDetRepo);
            }

            foreach ($this->flatValues as $flatKey => $flatValue) {
                $formula = $this->convertConstantToValue($formula, $flatKey, $flatValue, $this->flatValueDetRepo);
            }

            foreach (self::VARIABLES as $variable) {
                $formula = $this->convertVariableToValue($formula, $variable);
            }

            foreach (self::SYSTEM_RULE as $systemRule) {
                $formula = $this->convertSystemRuleToValue($formula, $systemRule);
            }

            $processedformula = $this->convertReferencingRuleToValue($formula, $refRules);

            $ruleValue = eval("return {$processedformula} ;");

            array_push($this->ruleDetailList, ["ruleValue" => $ruleValue, "rule" => $ruleDetail]);

            switch ($operationType) {
                case 'A':
                    $this->calculatedValue = $this->calculatedValue + $ruleValue;
                    break;
                case 'D':
                    $this->calculatedValue = $this->calculatedValue - $ruleValue;
                    break;
                case 'V':
                    break;
            }

            $ruleValueMap[$ruleId] = $ruleValue;
            $counter++;
        }

        return ["ruleValueKV" => $ruleValueMap, "calculatedValue" => $this->calculatedValue];
    }

    private function getMonthlyValues() {
        $monthlyValueRepo = new MonthlyValueRepository($this->adapter);
        $monthlyValueList = $monthlyValueRepo->fetchAll();
        return Helper::extractDbData($monthlyValueList);
    }

    private function getFlatValues() {
        $flatValueRepo = new FlatValueRepository($this->adapter);
        $flatValueList = $flatValueRepo->fetchAll();
        return Helper::extractDbData($flatValueList);
    }

    private function getReferencingRules($payId = null) {
        $referencingruleList = $this->repository->fetchReferencingRules($payId);
        return Helper::extractDbData($referencingruleList);
    }

    private function sanitizeString($input) {
        $processed = str_replace(" ", "_", $input);
        return strtoupper($processed);
    }

    private function convertConstantToValue($rule, $key, $constant, RepositoryInterface $repository) {
        if (strpos($rule, $constant) !== false) {
            return str_replace($constant, $this->generateValue($key, $repository), $rule);
        } else {
            return $rule;
        }
    }

    private function generateValue($key, RepositoryInterface $repository) {
        if ($repository instanceof MonthlyValueDetailRepo) {
            $monthlyValTmp = $repository->fetchById(['employeeId' => $this->employeeId, 'monthId' => $this->monthId, 'mthId' => $key]);
            $monthlyValTmp = (!isset($monthlyValTmp)) ? 0 : $monthlyValTmp;
            return $monthlyValTmp;
        } else if ($repository instanceof FlatValueDetailRepo) {
            $flatValTmp = $repository->fetchById(['employeeId' => $this->employeeId, 'monthId' => $this->monthId, 'flatId' => $key]);
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
            $payEdesc = $this->sanitizeString($refRule['PAY_EDESC']);
            if (strpos($rule, $this->wrapWithSmallBracket($payEdesc)) !== false) {
                $processedRefRules = 0;
                foreach ($this->ruleDetailList as $ruleDetail) {
                    if ($ruleDetail['rule']['PAY_ID'] == $refRule['PAY_ID']) {
                        if (!isset($ruleDetail['ruleValue'])) {
                            throw new Exception("Referencing Rule not set.");
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
