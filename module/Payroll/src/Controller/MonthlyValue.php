<?php

namespace Payroll\Controller;

use Application\Helper\ConstraintHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Payroll\Repository\MonthlyValueRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Payroll\Form\MonthlyValue as MonthlyValueForm;
use Payroll\Model\MonthlyValue as MonthlyValueModel;

class MonthlyValue extends AbstractActionController
{
    private $adapter;
    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new MonthlyValueRepository($adapter);
    }

    public function initializeForm()
    {
        $builder = new AnnotationBuilder();
        $monthlyValueForm = new MonthlyValueForm();
        $this->form = $builder->createForm($monthlyValueForm);
    }

    public function indexAction()
    {
        return Helper::addFlashMessagesToArray($this, [
            'monthlyValues' => $this->repository->fetchAll(),
            'constraint' => ConstraintHelper::CONSTRAINTS['YN']
        ]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $monthlyValue = new MonthlyValueModel();
                $monthlyValue->exchangeArrayFromForm($this->form->getData());
                $monthlyValue->mthId = ((int)Helper::getMaxId($this->adapter, MonthlyValueModel::TABLE_NAME, MonthlyValueModel::MTH_ID)) + 1;
                $monthlyValue->createdDt = Helper::getcurrentExpressionDate();
                $monthlyValue->status = 'E';
                $this->repository->add($monthlyValue);
                $this->flashmessenger()->addMessage("Monthly Value added Successfully!!");
                return $this->redirect()->toRoute("monthlyValue");
            }
        }
        return Helper::addFlashMessagesToArray($this,
            [
                'form' => $this->form,
                'customRenderer' => Helper::renderCustomView()
            ]
        );

    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        $this->initializeForm();
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

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('monthlyValue');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Monthly Value Successfully Deleted!!!");
        return $this->redirect()->toRoute('monthlyValue');
    }

    public function detailAction()
    {
        $branches = EntityHelper::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME]);
        $departments = EntityHelper::getTableKVList($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME]);
        $designations = EntityHelper::getTableKVList($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE]);
        $monthlyValues = EntityHelper::getTableKVList($this->adapter, MonthlyValueModel::TABLE_NAME, MonthlyValueModel::MTH_ID, [MonthlyValueModel::MTH_EDESC]);

        return Helper::addFlashMessagesToArray($this, [
            'branches' => $branches,
            'departments' => $departments,
            'designations' => $designations,
            'monthlyValues'=>$monthlyValues
        ]);
    }
}