<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 1:08 PM
 */

namespace Payroll\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use Payroll\Form\Rules as RuleForm;
use Payroll\Model\FlatValue;
use Payroll\Model\MonthlyValue;
use Payroll\Repository\RulesRepository;
use Setup\Model\Position;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class Rules extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new RulesRepository($adapter);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $ruleForm = new RuleForm();
        $this->form = $builder->createForm($ruleForm);
    }

    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, [
                    'rules' => $this->repository->fetchAll(),
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $monthlyValues = EntityHelper::getTableKVList($this->adapter, MonthlyValue::TABLE_NAME, MonthlyValue::MTH_ID, [MonthlyValue::MTH_EDESC]);
        $flatValues = EntityHelper::getTableKVList($this->adapter, FlatValue::TABLE_NAME, FlatValue::FLAT_ID, [FlatValue::FLAT_EDESC]);
        $positions = EntityHelper::getTableKVList($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME]);

        return Helper::addFlashMessagesToArray($this, [
                    'monthlyValues' => $monthlyValues,
                    'flatValues' => $flatValues,
                    'positions' => $positions,
                    "variables" => PayrollGenerator::VARIABLES,
                    "systemRules" => PayrollGenerator::SYSTEM_RULE,
                    'id' => $id
                        ]
        );
    }

}
