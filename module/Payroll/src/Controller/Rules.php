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
use Application\Model\FiscalYear;
use Payroll\Form\Rules as RuleForm;
use Payroll\Model\FlatValue;
use Payroll\Model\MonthlyValue;
use Payroll\Repository\RulesRepository;
use Setup\Model\Gender;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
        $ruleList = $this->repository->fetchAll();
        $rules = [];
        foreach ($ruleList as $ruleRow) {
            array_push($rules, $ruleRow);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'rules' => $rules
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $monthlyValues = EntityHelper::getTableKVList($this->adapter, MonthlyValue::TABLE_NAME, MonthlyValue::MTH_ID, [MonthlyValue::MTH_EDESC], [MonthlyValue::STATUS => 'E'],null,null,null,false,true);
        $flatValues = EntityHelper::getTableKVList($this->adapter, FlatValue::TABLE_NAME, FlatValue::FLAT_ID, [FlatValue::FLAT_EDESC], [MonthlyValue::STATUS => 'E'],null,null,null,false,true);
        $positions = EntityHelper::getTableKVList($this->adapter, Position::TABLE_NAME, Position::POSITION_ID, [Position::POSITION_NAME], [Position::STATUS => 'E'],null,null,null,false,true);
        $fiscalYears = EntityHelper::getTableKVList($this->adapter, FiscalYear::TABLE_NAME, FiscalYear::FISCAL_YEAR_ID, [FiscalYear::START_DATE, FiscalYear::END_DATE], [FiscalYear::STATUS => 'E'],null,null, "-",false,true);
        $genders = EntityHelper::getTableKVList($this->adapter, Gender::TABLE_NAME, Gender::GENDER_ID, [Gender::GENDER_NAME], [Gender::STATUS => 'E'],null,null, null, false,true);
        $serviceTypes = EntityHelper::getTableKVList($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID, [ServiceType::SERVICE_TYPE_NAME], [ServiceType::STATUS => 'E'],null,null, null, false,true);

        return Helper::addFlashMessagesToArray($this, [
                    'monthlyValues' => $monthlyValues,
                    'flatValues' => $flatValues,
                    'positions' => $positions,
                    "variables" => PayrollGenerator::VARIABLES,
                    "systemRules" => PayrollGenerator::SYSTEM_RULE,
                    'id' => $id,
                    'fiscalYears' => $fiscalYears,
                    'genders' => $genders,
                    'serviceTypes' => $serviceTypes]
        );
    }

    public function pullReferencedRulesAction() {
        $request = $this->getRequest();
        $data = [];
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $payId = $postedData['payId'];
            $refRules = $this->repository->fetchReferencingRules($payId);
            $data = Helper::extractDbData($refRules);
        } else {
            $data = ['success' => FALSE, 'message' => 'Request should be of post method'];
        }

        $view = new ViewModel(['data' => $data]);
        $view->setTerminal(true);
        $view->setTemplate('layout/json');
        return $view;
    }

}
