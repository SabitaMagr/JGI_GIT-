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
use Payroll\Model\FlatValue;
use Payroll\Model\MonthlyValue;
use Payroll\Repository\RulesRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\SessionManager;

use Payroll\Model\Rules as RulesModel;


class Rules extends AbstractActionController
{
    private $adapter;
    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new RulesRepository($adapter);
    }

    public function initializeForm()
    {
        $builder = new AnnotationBuilder();
        $ruleForm = new \Payroll\Form\Rules();
        $this->form = $builder->createForm($ruleForm);
    }

    public function indexAction()
    {
//        print_r($auth->getStorage()->read()['user_id']);
        return Helper::addFlashMessagesToArray($this, [
            'rules' => $this->repository->fetchAll(),
        ]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $monthlyValues = EntityHelper::getTableKVList($this->adapter, MonthlyValue::TABLE_NAME, MonthlyValue::MTH_ID, [MonthlyValue::MTH_EDESC]);
        $flatValues = EntityHelper::getTableKVList($this->adapter, FlatValue::TABLE_NAME, FlatValue::FLAT_ID, [FlatValue::FLAT_EDESC]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $rulesValue = new RulesModel();
                $rulesValue->exchangeArrayFromForm($this->form->getData());
                $rulesValue->payId = ((int)Helper::getMaxId($this->adapter, RulesModel::TABLE_NAME, RulesModel::PAY_ID)) + 1;
                $rulesValue->createdDt = Helper::getcurrentExpressionDate();
                $rulesValue->status = 'E';
                $rulesValue->refRuleFlag = 'N';

                $auth = new AuthenticationService();
                $rulesValue->createdBy = $auth->getStorage()->read()['user_id'];

                $this->repository->add($rulesValue);
                $this->flashmessenger()->addMessage("Rule added Successfully!!");
                return $this->redirect()->toRoute("rules");
            }
        }
        return Helper::addFlashMessagesToArray($this,
            [
                'form' => $this->form,
                'customRenderer' => Helper::renderCustomView(),
                'monthlyValues' => $monthlyValues,
                'flatValues' => $flatValues
            ]
        );

    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute("id");
        $monthlyValues = EntityHelper::getTableKVList($this->adapter, MonthlyValue::TABLE_NAME, MonthlyValue::MTH_ID, [MonthlyValue::MTH_EDESC]);
        $flatValues = EntityHelper::getTableKVList($this->adapter, FlatValue::TABLE_NAME, FlatValue::FLAT_ID, [FlatValue::FLAT_EDESC]);

        return Helper::addFlashMessagesToArray($this,
            [
                'monthlyValues' => $monthlyValues,
                'flatValues' => $flatValues,
                'id' => $id
            ]
        );
    }

}