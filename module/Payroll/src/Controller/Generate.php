<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/20/16
 * Time: 4:32 PM
 */

namespace Payroll\Controller;


use Application\Helper\EntityHelper;
use Payroll\Model\RulesDetail;
use Payroll\Repository\RulesDetailRepo;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Payroll\Model\MonthlyValue as MonthlyValueModel;
use Payroll\Model\FlatValue as FlatValueModel;


class Generate extends AbstractActionController
{

    private $adapter;
    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
//        $this->repository = new FlatValueRepository($adapter);
    }

    public function indexAction()
    {
        $monthlyValues = EntityHelper::getTableKVList($this->adapter, MonthlyValueModel::TABLE_NAME, MonthlyValueModel::MTH_ID, [MonthlyValueModel::MTH_EDESC]);
        $flatValues = EntityHelper::getTableKVList($this->adapter, FlatValueModel::TABLE_NAME, FlatValueModel::FLAT_ID, [FlatValueModel::FLAT_EDESC]);

        $this->sanitizeStringArray($monthlyValues);
        $this->sanitizeStringArray($flatValues);

        print "<pre>";
        $employeeId = 1;

        $ruleDetailRepo = new RulesDetailRepo($this->adapter);
        $rule = $ruleDetailRepo->fetchById(1)->{RulesDetail::MNENONIC_NAME};
        exit;
    }

    private function sanitizeStringArray(array &$stringArray)
    {
        foreach ($stringArray as &$string) {
            $string = str_replace(" ", "_", $string);
        }
    }

    private function convertConstantToValue($rule,$constant){
            if(strpos($constant,$rule)>=0){
               $rule=str_replace($constant,$this->generateValue($constant),$rule) ;
                return $this->convertConstantToValue($rule,$constant);
            }else{
                return $rule;
            }

    }

    private function generateValue($constant){
        return 1;
    }
}