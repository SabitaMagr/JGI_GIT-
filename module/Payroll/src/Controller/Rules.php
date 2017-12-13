<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Exception;
use Payroll\Form\Rules as RuleForm;
use Payroll\Model\Rules as RulesModel;
use Payroll\Repository\FlatValueRepository;
use Payroll\Repository\MonthlyValueRepository;
use Payroll\Repository\RulesRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class Rules extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(RulesRepository::class);
        $this->initializeForm(RuleForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
//                $data = $request->getPost();
                $rawList = $this->repository->fetchAll();
                $list = Helper::extractDbData($rawList);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return $this->stickFlashMessagesTo(['acl' => $this->acl]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $ruleModel = new RulesModel();
                $ruleModel->exchangeArrayFromForm($this->form->getData());
                $ruleModel->payId = ((int) Helper::getMaxId($this->adapter, RulesModel::TABLE_NAME, RulesModel::PAY_ID)) + 1;
                $ruleModel->createdDt = Helper::getcurrentExpressionDate();
                $ruleModel->createdBy = $this->employeeId;
                $ruleModel->status = 'E';

                $this->repository->add($ruleModel);

                $this->flashmessenger()->addMessage("Rule successfully added.");
                return $this->redirect()->toRoute("rules");
            }
        }
        $formulaData['monthlyValueList'] = $this->getMonthlyValues();
        $formulaData['flatValueList'] = $this->getFlatValues();
        $formulaData['variableList'] = PayrollGenerator::VARIABLES;
        $formulaData['systemRuleList'] = PayrollGenerator::SYSTEM_RULE;
        $formulaData['referencingRuleList'] = $this->getReferencingRules();

        return [
            'form' => $this->form,
            'customRenderer' => Helper::renderCustomView(),
            'formulaData' => json_encode($formulaData),
        ];
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

    public function editAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('rules');
        }
        $ruleModel = new RulesModel();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $ruleModel->exchangeArrayFromForm($this->form->getData());
                $ruleModel->modifiedDt = Helper::getcurrentExpressionDate();
                $ruleModel->modifiedBy = $this->employeeId;

                $this->repository->edit($ruleModel, $id);

                $this->flashmessenger()->addMessage("Rule successfully edited.");
                return $this->redirect()->toRoute("rules");
            }
        }
        $ruleModel->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
        $this->form->bind($ruleModel);
        $formulaData['monthlyValueList'] = $this->getMonthlyValues();
        $formulaData['flatValueList'] = $this->getFlatValues();
        $formulaData['variableList'] = PayrollGenerator::VARIABLES;
        $formulaData['systemRuleList'] = PayrollGenerator::SYSTEM_RULE;
        $formulaData['referencingRuleList'] = $this->getReferencingRules($id);

        return [
            'id' => $id,
            'form' => $this->form,
            'customRenderer' => Helper::renderCustomView(),
            'formulaData' => json_encode($formulaData),
        ];
    }

}
