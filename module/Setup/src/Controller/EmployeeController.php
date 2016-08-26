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
use Zend\Db\Sql\Sql;
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
                $employee->employeeId = ((int)Helper::getMaxId($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID")) + 1;
                $employee->status = 'E';
                $employee->createdDt = Helper::getcurrentExpressionDate();
                $employee->birthDate = Helper::getExpressionDate($employee->birthDate);
                $employee->famSpouseBirthDate = Helper::getExpressionDate($employee->famSpouseBirthDate);
                $employee->famSpouseWeddingAnniversary = Helper::getExpressionDate($employee->famSpouseWeddingAnniversary);
                $employee->idDrivingLicenseExpiry = Helper::getExpressionDate($employee->idDrivingLicenseExpiry);
                $employee->idCitizenshipIssueDate = Helper::getExpressionDate($employee->idCitizenshipIssueDate);
                $employee->idPassportExpiry = Helper::getExpressionDate($employee->idPassportExpiry);
                $employee->joinDate = Helper::getExpressionDate($employee->joinDate);

                $this->repository->add($employee);

                return $this->redirect()->toRoute("employee");
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
            "companies" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COMPANY),
            "countries" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COUNTRIES)
        ]);


    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('employee', ['action' => 'index']);
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
                $employee->birthDate = Helper::getExpressionDate($employee->birthDate);
                $employee->famSpouseBirthDate = Helper::getExpressionDate($employee->famSpouseBirthDate);
                $employee->famSpouseWeddingAnniversary = Helper::getExpressionDate($employee->famSpouseWeddingAnniversary);
                $employee->idDrivingLicenseExpiry = Helper::getExpressionDate($employee->idDrivingLicenseExpiry);
                $employee->idCitizenshipIssueDate = Helper::getExpressionDate($employee->idCitizenshipIssueDate);
                $employee->idPassportExpiry = Helper::getExpressionDate($employee->idPassportExpiry);
                $employee->joinDate = Helper::getExpressionDate($employee->joinDate);

                $this->repository->edit($employee, $id);
                $this->flashmessenger()->addMessage("Employee Successfully Updated!!!");
                return $this->redirect()->toRoute("employee");
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
            "companies" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COMPANY),
            "countries" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_COUNTRIES)
        ]);


    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");

        $this->repository->delete($id);

        $this->flashmessenger()->addMessage("Employee Successfully Deleted!!!");
        return $this->redirect()->toRoute('employee');
    }

}
