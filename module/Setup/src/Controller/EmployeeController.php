<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 7/29/16
 * Time: 11:02 AM
 */

namespace Setup\Controller;


use Application\Helper\Helper;
use Setup\Form\HrEmployeesForm;
use Setup\Helper\EntityHelper;
use Setup\Model\HrEmployees;
use Setup\Repository\EmployeeRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Hydrator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class EmployeeController extends AbstractActionController
{
    private $adapter;
    private $form;
    private $repository;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new EmployeeRepository($adapter);

    }


    public function indexAction()
    {
        $employees = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['list' => $employees]);
    }

    public function addAction()
    {
        $employeeForm = new HrEmployeesForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($employeeForm);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $employee = new HrEmployees();
                $employee->exchangeArrayFromForm($this->form->getData());
                $employee->employeeId = 1;
                $employee->status = 'E';
                $employee->createdDt = '14-AUG-15';

                $this->repository->add($employee);

                return $this->redirect()->toRoute("setup");
            }
        }
        return new ViewModel([
            'form' => $this->form,
            "bloodGroups" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_BLOOD_GROUPS),
            "districts" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_DISTRICTS),
            "genders" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_GENDERS),
            "vdcMunicipalities" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_VDC_MUNICIPALITY),
            "zones" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_ZONES),
            "religions" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_RELIGIONS),
            "companies" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COMPANY)
        ]);


    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('setup', ['action' => 'index']);
        }


        $employeeForm = new HrEmployeesForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($employeeForm);
        }

        $request = $this->getRequest();
        if (!$request->isPost()) {
            $employee = new HrEmployees();
            $employee->exchangeArrayFromDB((array)$this->repository->fetchById($id));
            $this->form->bind($employee);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $employee = new HrEmployees();
                $employee->exchangeArrayFromForm($this->form->getData());
                $employee->employeeId = $id;
                $this->repository->edit($employee, $id, "1-AUG-12");
                $this->flashmessenger()->addMessage("Employee Successfully Updated!!!");
                return $this->redirect()->toRoute("setup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            "id" => $id,
            "bloodGroups" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_BLOOD_GROUPS),
            "districts" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_DISTRICTS),
            "genders" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_GENDERS),
            "vdcMunicipalities" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_VDC_MUNICIPALITY),
            "zones" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_ZONES),
            "religions" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_RELIGIONS),
            "companies" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COMPANY)
        ]);


    }

}
