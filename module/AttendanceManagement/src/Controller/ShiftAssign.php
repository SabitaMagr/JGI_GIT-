<?php
namespace AttendanceManagement\Controller;


use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use AttendanceManagement\Model\ShiftSetup;
use AttendanceManagement\Repository\ShiftAssignRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ShiftAssign extends AbstractActionController
{
    private $repository;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new ShiftAssignRepository($adapter);
        $this->adapter=$adapter;
    }

    public function indexAction()
    {
        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches=\Application\Helper\EntityHelper::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME]);
        $branches[-1]="All";
        ksort($branches);
        $branchFormElement->setValueOptions($branches);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");
        $branchFormElement->setAttribute("ng-click","view()");


        $departmentFormElement = new Select();
        $departmentFormElement->setName("department");
        $departments=\Application\Helper\EntityHelper::getTableKVList($this->adapter, Department::TABLE_NAME, Department::DEPARTMENT_ID, [Department::DEPARTMENT_NAME]);
        $departments[-1]="All";
        ksort($departments);
        $departmentFormElement->setValueOptions($departments);
        $departmentFormElement->setAttributes(["id" => "departmentId", "class" => "form-control"]);
        $departmentFormElement->setLabel("Department");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations=\Application\Helper\EntityHelper::getTableKVList($this->adapter,Designation::TABLE_NAME,Designation::DESIGNATION_ID , [Designation::DESIGNATION_TITLE]);
        $designations[-1]="All";
        ksort($designations);
        $designationFormElement->setValueOptions($designations);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control"]);
        $designationFormElement->setLabel("Designation");

        $positionFormElement = new Select();
        $positionFormElement->setName("position");
        $positions=\Application\Helper\EntityHelper::getTableKVList($this->adapter,Position::TABLE_NAME,Position::POSITION_ID , [Position::POSITION_NAME]);
        $positions[-1]="All";
        ksort($positions);
        $positionFormElement->setValueOptions($positions);
        $positionFormElement->setAttributes(["id" => "positionId", "class" => "form-control"]);
        $positionFormElement->setLabel("Position");

        $serviceTypeFormElement = new Select();
        $serviceTypeFormElement->setName("serviceType");
        $serviceTypes=\Application\Helper\EntityHelper::getTableKVList($this->adapter,ServiceType::TABLE_NAME,ServiceType::SERVICE_TYPE_ID , [ServiceType::SERVICE_TYPE_NAME]);
        $serviceTypes[-1]="All";
        ksort($serviceTypes);
        $serviceTypeFormElement->setValueOptions($serviceTypes);
        $serviceTypeFormElement->setAttributes(["id" => "serviceTypeId", "class" => "form-control"]);
        $serviceTypeFormElement->setLabel("Service Type");

        $shifts=EntityHelper::getTableKVList($this->adapter,ShiftSetup::TABLE_NAME,ShiftSetup::SHIFT_ID,[ShiftSetup::SHIFT_ENAME]);

        $shiftFormElement = new Select();
        $shiftFormElement->setName("shift");
        $shiftFormElement->setValueOptions($shifts);
        $shiftFormElement->setAttributes(["id" => "shiftId", "class" => "form-control"]);
        $shiftFormElement->setLabel("Shift");

        return new ViewModel([
            "branches"=>$branchFormElement,
            "departments"=>$departmentFormElement,
            'designations'=>$designationFormElement,
            'positions'=>$positionFormElement,
            'serviceTypes'=>$serviceTypeFormElement,
            'shiftFormElement'=>$shiftFormElement
        ]);
    }


}