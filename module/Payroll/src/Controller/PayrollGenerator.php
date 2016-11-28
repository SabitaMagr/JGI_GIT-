<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/7/16
 * Time: 12:17 PM
 */

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

class PayrollGenerator
{
    private $adapter;
    private $flatValueDetRepo;
    private $monthlyValueDetRepo;
    private $payPositionRepo;
    private $ruleDetailRepo;
    private $ruleRepo;
    private $employeeId;

    private $monthlyValues;
    private $flatValues;

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
        "TOTAL_DAYS_FROM_JOIN_DATE"
    ];

    const SYSTEM_RULE = [
        "LEAST_VALUE",
        "GREATEST_VALUE",
        "CALC_BASIC_CURRENT",
        "CALC_BASIC_OLD",
        "SUM_VALUE",
        "MONTH",
        "JOIN_MONTH",
        "AGE",
        "CURRENT_MONTH",
        "EMPLOYEE_GRADE"
    ];


    public function __construct($adapter)
    {
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

    private function getPositionId($id)
    {
        return EntityHelper::getTableKVList($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["POSITION_ID"], ["EMPLOYEE_ID" => $id], null)[$id];
    }

    public function generate($id)
    {
        $this->employeeId = $id;

        $positionId = $this->getPositionId($id);
        $payPositionList = $this->payPositionRepo->fetchByPositionId($positionId);

        $payList = [];

        foreach ($payPositionList as $payPosition) {
            array_push($payList, $payPosition[PayPositionSetup::PAY_ID]);
        }

        $calculatedValue = 0;
        $ruleValueKV = [];

        foreach ($payList as $ruleId) {
            $rule = $this->ruleDetailRepo->fetchById($ruleId)->{RulesDetail::MNENONIC_NAME};
            $ruleObj = $this->ruleRepo->fetchById($ruleId);
            $operationType = $ruleObj->{Rules::PAY_TYPE_FLAG};

            foreach ($this->monthlyValues as $key => $monthlyValue) {
                $rule = $this->convertConstantToValue($rule, $key, $monthlyValue, $this->monthlyValueDetRepo);
            }

            foreach ($this->flatValues as $key => $flatValue) {
                $rule = $this->convertConstantToValue($rule, $key, $flatValue, $this->flatValueDetRepo);
            }

            foreach (self::VARIABLES as $variable) {
                $rule = $this->convertVariableToValue($rule, $variable);
            }
            $ruleValue = eval($rule);
            if ($operationType == 'A') {
                $calculatedValue = $calculatedValue + $ruleValue;
            } else if ($operationType == 'D') {
                $calculatedValue = $calculatedValue - $ruleValue;
            }
            $ruleValueKV[$ruleId] = $ruleValue;
        }

        return ["ruleValueKV" => $ruleValueKV, "calculatedValue" => $calculatedValue];
    }

    private function sanitizeStringArray(array &$stringArray)
    {
        foreach ($stringArray as &$string) {
            $string = str_replace(" ", "_", $string);
            $string = strtoupper($string);
        }
    }

    private function convertConstantToValue($rule, $key, $constant, RepositoryInterface $repository)
    {
        if (strpos($rule, $constant) !== false) {
            return str_replace($constant, $this->generateValue($key, $repository), $rule);
        } else {
            return $rule;
        }
    }

    private function generateValue($constant, RepositoryInterface $repository)
    {
        if ($repository instanceof MonthlyValueDetailRepo) {
            return $repository->fetchById([$this->employeeId, $constant])[MonthlyValueDetail::MTH_VALUE];
        } else if ($repository instanceof FlatValueDetailRepo) {
            return $repository->fetchById([$this->employeeId, $constant])[FlatValueDetail::FLAT_VALUE];
        }

    }

    private function convertVariableToValue($rule, $variable)
    {
        if (strpos($rule, $variable) !== false) {
            $variableProcessor = new VariableProcessor($this->adapter, $this->employeeId);
            return str_replace($variable, $variableProcessor->processVariable($variable), $rule);
        } else {
            return $rule;
        }

    }

}