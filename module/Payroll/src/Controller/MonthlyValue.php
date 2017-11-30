<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\ConstraintHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Application\Model\Months;
use Exception;
use Payroll\Form\MonthlyValue as MonthlyValueForm;
use Payroll\Model\MonthlyValue as MonthlyValueModel;
use Payroll\Repository\MonthlyValueDetailRepo;
use Payroll\Repository\MonthlyValueRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class MonthlyValue extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(MonthlyValueRepository::class);
        $this->initializeForm(MonthlyValueForm::class);
    }

    public function indexAction() {
        $constraint = ConstraintHelper::CONSTRAINTS['YN'];
        $monthlyValueList = $this->repository->fetchAll();
        $montlyValues = [];
        foreach ($monthlyValueList as $monthlyValueRow) {
            $showAtRule = $constraint[$monthlyValueRow['SHOW_AT_RULE']];
            $rowRecord = $monthlyValueRow->getArrayCopy();
            $new_row = array_merge($rowRecord, ['SHOW_AT_RULE' => $showAtRule]);
            array_push($montlyValues, $new_row);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'monthlyValues' => $montlyValues
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $monthlyValue = new MonthlyValueModel();
                $monthlyValue->exchangeArrayFromForm($this->form->getData());
                $monthlyValue->mthId = ((int) Helper::getMaxId($this->adapter, MonthlyValueModel::TABLE_NAME, MonthlyValueModel::MTH_ID)) + 1;
                $monthlyValue->createdDt = Helper::getcurrentExpressionDate();
                $monthlyValue->status = 'E';
                $this->repository->add($monthlyValue);
                $this->flashmessenger()->addMessage("Monthly Value added Successfully!!");
                return $this->redirect()->toRoute("monthlyValue");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView()
                        ]
        );
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $request = $this->getRequest();

        $monthlyValueMode = new MonthlyValueModel();
        if (!$request->isPost()) {
            $monthlyValueMode->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($monthlyValueMode);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $monthlyValueMode->exchangeArrayFromForm($this->form->getData());
                $monthlyValueMode->modifiedDt = Helper::getcurrentExpressionDate();
                unset($monthlyValueMode->createdDt);
                unset($monthlyValueMode->mthId);
                unset($monthlyValueMode->status);
                $this->repository->edit($monthlyValueMode, $id);
                $this->flashmessenger()->addMessage("Monthly Value updated successfully!!!");
                return $this->redirect()->toRoute("monthlyValue");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('monthlyValue');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Monthly Value Successfully Deleted!!!");
        return $this->redirect()->toRoute('monthlyValue');
    }

    public function detailAction() {
        $monthlyValues = EntityHelper::getTableList($this->adapter, MonthlyValueModel::TABLE_NAME, [MonthlyValueModel::MTH_ID, MonthlyValueModel::MTH_EDESC]);
        $fiscalYears = EntityHelper::getTableList($this->adapter, FiscalYear::TABLE_NAME, [FiscalYear::FISCAL_YEAR_ID, FiscalYear::FISCAL_YEAR_NAME]);
        $months = EntityHelper::getTableList($this->adapter, Months::TABLE_NAME, [Months::MONTH_ID, Months::MONTH_EDESC, Months::FISCAL_YEAR_ID]);
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'monthlyValues' => $monthlyValues,
                    'fiscalYears' => $fiscalYears,
                    'months' => $months
        ]);
    }

    public function getMonthlyValueDetailAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $postedData = $request->getPost();
            $mthId = $postedData['mthId'];
            $fiscalYearId = $postedData['fiscalYearId'];
            $employeeFilter = $postedData['employeeFilter'];

            $detailRepo = new MonthlyValueDetailRepo($this->adapter);
            $result = $detailRepo->getMonthlyValuesDetailById($mthId, $fiscalYearId, $employeeFilter);

            return new CustomViewModel(['success' => true, 'data' => Helper::extractDbData($result), 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function postMonthlyValueDetailAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $postedData = $request->getPost();
            $data = $postedData['data'];
            $detailRepo = new MonthlyValueDetailRepo($this->adapter);
            $detailRepo->postMonthlyValuesDetail($data);

            return new CustomViewModel(['success' => true, 'data' => $data, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
