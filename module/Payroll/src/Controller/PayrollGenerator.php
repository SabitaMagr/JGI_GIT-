<?php

namespace Payroll\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\RepositoryInterface;
use Payroll\Model\FlatValue as FlatValueModel;
use Payroll\Model\FlatValueDetail;
use Payroll\Model\MonthlyValue as MonthlyValueModel;
use Payroll\Model\MonthlyValueDetail;
use Payroll\Model\PayPositionSetup;
use Payroll\Model\Rules;
use Payroll\Model\RulesDetail;
use Payroll\Repository\FlatValueDetailRepo;
use Payroll\Repository\MonthlyValueDetailRepo;
use Payroll\Repository\PayPositionRepo;
use Payroll\Repository\RulesDetailRepo;
use Payroll\Repository\RulesRepository;

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
    private $monthId = 0;

    const VARIABLES = [
        "BASIC_SALARY",
        "NO_OF_WORKING_DAYS",
        "NO_OF_DAYS_ABSENT",
        "NO_OF_DAYS_WORKED",
        "NO_OF_PAID_LEAVES",
        "NO_OF_UNPAID_LEAVES",
        "GENDER",
        "EMP_TYPE",
        "MARITUAL_STATUS",
        "TOTAL_DAYS_FROM_JOIN_DATE",
        "SERVICE_TYPE",
        "NO_OF_WORKING_DAYS_INC_HOLIDAYS",
        "TOTAL_NO_OF_WORK_DAYS_INC_HOLIDAYS",
        "SALARY_REVIEW_DAY",
        "SALARY_REVIEW_OLD_SALARY",
        "HAS_ADVANCE"
    ];
    const SYSTEM_RULE = [
        "CUR_MTH_ID",
        "MTH_RESULT",
        "YR_RESULT",
        "CUR_MTH_LAST_DAY",
    ];

    public function __construct($adapter, int $monthId) {
        $this->adapter = $adapter;
        $this->monthId = $monthId;
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
        $counter = 0;
        foreach ($payList as $ruleObj) {
            $ruleId = $ruleObj[PayPositionSetup::PAY_ID];
            $ruleDetail = $this->ruleDetailRepo->fetchById($ruleId)->getArrayCopy();
            $rule = $ruleDetail[RulesDetail::MNENONIC_NAME];
            $operationType = $ruleObj[Rules::PAY_TYPE_FLAG];

            $ruleRepo = new RulesRepository($this->adapter);
            $refRules = $ruleRepo->fetchReferencingRules($ruleId);
            $refRules = Helper::extractDbData($refRules);

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
//            if ($ruleDetail['PAY_ID'] == 11) {
//                print "<pre>";
//                print_r("return " . $rule . " ;");
//                exit;
//            }
            $rule = $this->convertReferencingRuleToValue($rule, $refRules);

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
            $monthlyValTmp = $repository->fetchById([$this->employeeId, $constant])[MonthlyValueDetail::MTH_VALUE];
            $monthlyValTmp = (!isset($monthlyValTmp)) ? 0 : $monthlyValTmp;
            return $monthlyValTmp;
        } else if ($repository instanceof FlatValueDetailRepo) {
            $flatValTmp = $repository->fetchById([$this->employeeId, $constant])[FlatValueDetail::FLAT_VALUE];
            $flatValTmp = (isset($flatValTmp)) ? $flatValTmp : 0;
            return $flatValTmp;
        }
    }

    private function convertVariableToValue($rule, $variable) {
        if (strpos($rule, $this->wrapWithLargeBracket($variable)) !== false) {
            $variableProcessor = new VariableProcessor($this->adapter, $this->employeeId, $this->monthId);
//            return str_replace($variable, $variableProcessor->processVariable($variable), $rule);
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
