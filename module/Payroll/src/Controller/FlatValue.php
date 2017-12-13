<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Exception;
use Payroll\Form\FlatValue as FlatValueForm;
use Payroll\Model\FlatValue as FlatValueModel;
use Payroll\Repository\FlatValueDetailRepo;
use Payroll\Repository\FlatValueRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class FlatValue extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(FlatValueRepository::class);
        $this->initializeForm(FlatValueForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
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
                $flatValue = new FlatValueModel();
                $flatValue->exchangeArrayFromForm($this->form->getData());
                $flatValue->flatId = ((int) Helper::getMaxId($this->adapter, FlatValueModel::TABLE_NAME, FlatValueModel::FLAT_ID)) + 1;
                $flatValue->createdDt = Helper::getcurrentExpressionDate();
                $flatValue->status = 'E';

                $this->repository->add($flatValue);
                $this->flashmessenger()->addMessage("Flat Value added Successfully.");
                return $this->redirect()->toRoute("flatValue");
            }
        }
        return [
            'form' => $this->form,
            'customRenderer' => Helper::renderCustomView()
        ];
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $request = $this->getRequest();

        $flatValueModel = new FlatValueModel();
        if (!$request->isPost()) {
            $flatValueModel->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($flatValueModel);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $flatValueModel->exchangeArrayFromForm($this->form->getData());
                $flatValueModel->modifiedDt = Helper::getcurrentExpressionDate();
                unset($flatValueModel->createdDt);
                unset($flatValueModel->flatId);
                unset($flatValueModel->status);
                $this->repository->edit($flatValueModel, $id);
                $this->flashmessenger()->addMessage("Flat Value updated successfully.");
                return $this->redirect()->toRoute("flatValue");
            }
        }
        return [
            'form' => $this->form,
            'id' => $id,
            'customRenderer' => Helper::renderCustomView()
        ];
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('flatValue');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Flat Value Successfully Deleted!!!");
        return $this->redirect()->toRoute('flatValue');
    }

    public function detailAction() {
        $flatValues = EntityHelper::getTableList($this->adapter, FlatValueModel::TABLE_NAME, [FlatValueModel::FLAT_ID, FlatValueModel::FLAT_EDESC]);
        $fiscalYears = EntityHelper::getTableList($this->adapter, FiscalYear::TABLE_NAME, [FiscalYear::FISCAL_YEAR_ID, FiscalYear::FISCAL_YEAR_NAME]);
        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'flatValues' => $flatValues,
                    'fiscalYears' => $fiscalYears,
        ]);
    }

    public function getFlatValueDetailAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $postedData = $request->getPost();
            $flatId = $postedData['flatId'];
            $fiscalYearId = $postedData['fiscalYearId'];
            $employeeFilter = $postedData['employeeFilter'];
            $detailRepo = new FlatValueDetailRepo($this->adapter);
            $result = $detailRepo->getFlatValuesDetailById($flatId, $fiscalYearId, $employeeFilter);

            return new CustomViewModel(['success' => true, 'data' => Helper::extractDbData($result), 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function postFlatValueDetailAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $postedData = $request->getPost();
            $data = $postedData['data'];
            $detailRepo = new FlatValueDetailRepo($this->adapter);
            $detailRepo->postFlatValuesDetail($data);

            return new CustomViewModel(['success' => true, 'data' => $data, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
