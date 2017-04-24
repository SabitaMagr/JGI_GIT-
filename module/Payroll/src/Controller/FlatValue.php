<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 10/3/16
 * Time: 1:36 PM
 */

namespace Payroll\Controller;


use Application\Helper\ConstraintHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Payroll\Repository\FlatValueRepository;
use Payroll\Service\TestService;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Payroll\Model\FlatValue as FlatValueModel;

class FlatValue extends AbstractActionController
{

    private $adapter;
    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new FlatValueRepository($adapter);
    }

    public function initializeForm()
    {
        $builder = new AnnotationBuilder();
        $flatValueForm = new \Payroll\Form\FlatValue();
        $this->form = $builder->createForm($flatValueForm);
    }

    public function indexAction()
    {
        $constraint = ConstraintHelper::CONSTRAINTS['YN'];
        $flatValueList = $this->repository->fetchAll();
        $flatValues = [];
        foreach($flatValueList as $flatValueRow){
            $showAtRule = $constraint[$flatValueRow['SHOW_AT_RULE']];
            $rowRecord = $flatValueRow->getArrayCopy();
            $new_row = array_merge($rowRecord,['SHOW_AT_RULE'=>$showAtRule]);
            array_push($flatValues, $new_row);
             
        }
        return Helper::addFlashMessagesToArray($this, [
            'flatValues' => $flatValues            
        ]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $flatValue = new FlatValueModel();
                $flatValue->exchangeArrayFromForm($this->form->getData());
                $flatValue->flatId = ((int)Helper::getMaxId($this->adapter, FlatValueModel::TABLE_NAME, FlatValueModel::FLAT_ID)) + 1;
                $flatValue->createdDt = Helper::getcurrentExpressionDate();
                $flatValue->status = 'E';

                $this->repository->add($flatValue);
                $this->flashmessenger()->addMessage("Flat Value added Successfully!!");
                return $this->redirect()->toRoute("flatValue");
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
                $this->flashmessenger()->addMessage("Flat Value updated successfully!!!");
                return $this->redirect()->toRoute("flatValue");
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
            return $this->redirect()->toRoute('flatValue');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Flat Value Successfully Deleted!!!");
        return $this->redirect()->toRoute('flatValue');
    }


    public function detailAction(){
        $branches = EntityHelper::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME],null,null,null,null,false,true);
        $departments = EntityHelper::getTableKVListWithSortOption($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME],null,null,null,null,false,true);
        $designations = EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE],null,null,null,null,false,true);
        $flatValues = EntityHelper::getTableKVListWithSortOption($this->adapter, FlatValueModel::TABLE_NAME, FlatValueModel::FLAT_ID, [FlatValueModel::FLAT_EDESC],null,null,null,null,false,true);

        return Helper::addFlashMessagesToArray($this, [
            'branches' => $branches,
            'departments' => $departments,
            'designations' => $designations,
            'flatValues'=>$flatValues
        ]);
    }
}