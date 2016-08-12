<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 7/29/16
 * Time: 11:02 AM
 */

namespace Setup\Controller;


use Doctrine\ORM\Mapping\Entity;
use SebastianBergmann\CodeCoverage\Node\Builder;
use Setup\Entity\BloodGroups;
use Setup\Entity\HrDistricts;
use Setup\Entity\HrEmployees;
use Setup\Entity\HrZones;
use Setup\Form\HrEmployeesForm;
use Setup\Helper\EntityHelper;
use Setup\Model\EmployeeRepository;
use Setup\Model\EmployeeRepositoryInterface;
use Test\Form\EmployeeForm;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Hydrator;

use Setup\Model\Employee;


use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;


class EmployeeController extends AbstractActionController
{
    private $entityManager;
    private $form;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function indexAction()
    {
        var_dump(EntityHelper::extract($this->entityManager, EntityHelper::hydrate($this->entityManager, HrDistricts::class, ['districtId' => 1, "districtName" => "sdf"])));

    }

    public function addAction()
    {
        $employeeForm = new HrEmployeesForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($employeeForm);
        }

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return new ViewModel([
                'form' => $this->form,
                "bloodGroups" => EntityHelper::getBloodGroupKVList($this->entityManager),
                "districts" => EntityHelper::getDistrictKVList($this->entityManager),
                "genders" => EntityHelper::getGenderKVList($this->entityManager),
                "vdcMunicipalities" => EntityHelper::getVdcMunicipalityKVList($this->entityManager),
                "zones" => EntityHelper::getZoneKVList($this->entityManager)
            ]);
        }

        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
            $employee = EntityHelper::hydrate($this->entityManager, HrEmployees::class, $this->form->getData());
            $this->entityManager->persist($employee);
            $this->entityManager->flush();

            return $this->redirect()->toRoute("setup");
        } else {
            return [
                "form" => $this->form,
                "districts" => EntityHelper::getDistrictKVList($this->entityManager),
                "bloodGroups" => EntityHelper::getBloodGroupKVList($this->entityManager),
                "genders" => EntityHelper::getGenderKVList($this->entityManager),
                "vdcMunicipalities" => EntityHelper::getVdcMunicipalityKVList($this->entityManager),
                "zones" => EntityHelper::getZoneKVList($this->entityManager)

            ];
        }

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
            $employee = $this->entityManager->find(HrEmployees::class, $id);
            $this->form->bind($employee);
            return new ViewModel([
                'form' => $this->form,
                "id" => $id,
                "bloodGroups" => EntityHelper::getBloodGroupKVList($this->entityManager),
                "districts" => EntityHelper::getDistrictKVList($this->entityManager),
                "genders" => EntityHelper::getGenderKVList($this->entityManager),
                "vdcMunicipalities" => EntityHelper::getVdcMunicipalityKVList($this->entityManager),
                "zones" => EntityHelper::getZoneKVList($this->entityManager)
            ]);
        }

        $this->form->setData($request->getPost());
        if (!$this->form->isValid()) {

            return new ViewModel([
                'form' => $this->form,
                "id" => $id,
                "bloodGroups" => EntityHelper::getBloodGroupKVList($this->entityManager),
                "districts" => EntityHelper::getDistrictKVList($this->entityManager),
                "genders" => EntityHelper::getGenderKVList($this->entityManager),
                "vdcMunicipalities" => EntityHelper::getVdcMunicipalityKVList($this->entityManager),
                "zones" => EntityHelper::getZoneKVList($this->entityManager)
            ]);
        }
        $employee = EntityHelper::hydrate($this->entityManager, HrEmployees::class, $this->form->getData());
        $employee->setEmployeeId($id);
        $this->entityManager->merge($employee);
        $this->entityManager->flush();

        return $this->redirect()->toRoute("setup");


    }


//    protected $form;
//    private $employeeRepository;
//
//    public function __construct(AdapterInterface $db)
//    {
//        $this->employeeRepository = new EmployeeRepository($db);
//
//    }
//
//
//    public function addAction()
//    {
//        $employee = new Employee();
//        $builder = new AnnotationBuilder();
//        if (!$this->form) {
//            $this->form = $builder->createForm($employee);
//        }
//
//        $request = $this->getRequest();
//        if (!$request->isPost()) {
//            return new ViewModel([
//                'form' => $this->form
//            ]);
//        }
//
//
//        $this->form->setData($request->getPost());
//
//        if ($this->form->isValid()) {
//            $employee->exchangeArray($this->form->getData());
//
//            $this->employeeRepository->add($employee);
//
//            return $this->redirect()->toRoute("setup");
//
//        } else {
//            return $this->redirect()->toRoute("123");
//
//        }
//
//
//    }
//
//    public function editAction()
//    {
//        $id = (int)$this->params()->fromRoute('id', 0);
//
//        if (0 === $id) {
//            return $this->redirect()->toRoute('setup', ['action' => 'index']);
//        }
//
//
//        $employee = new Employee();
//        $builder = new AnnotationBuilder();
//        if (!$this->form) {
//            $this->form = $builder->createForm($employee);
//        }
//
//
//        $request = $this->getRequest();
//        $viewData = [];
//
//        if (!$request->isPost()) {
//            $this->form->bind($this->employeeRepository->fetchById($id));
//
//            $viewData = ['id' => $id, 'form' => $this->form];
//            return $viewData;
//        }
//
//        $this->form->setData($request->getPost());
//
//        if (!$this->form->isValid()) {
//            return $viewData;
//        }
//
//        $employee->exchangeArray($this->form->getData());
//        $this->employeeRepository->edit($employee, $id);
//
//
//        return $this->redirect()->toRoute("setup");
//
//    }
//
//    public function indexAction()
//    {
////        $employeeTable = new TableGateway('employee', $this->db);
////
////
////        $rowset = $employeeTable->select(function (Select $select) {
////            $select->order('employeeCode desc');
////        });
//
//        $rowset=$this->employeeRepository->fetchAll();
//        return new ViewModel(['list' => $rowset]);
//
//
//    }


}