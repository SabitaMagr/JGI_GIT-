<?php
namespace AttendanceManagement\Controller;

use Application\Helper\EntityHelper;
use AttendanceManagement\Model\ShiftSetup;
use AttendanceManagement\Repository\ShiftAssignRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ShiftAssign extends AbstractActionController
{
    private $repository;
    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository = new ShiftAssignRepository($adapter);
        $this->adapter=$adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction()
    {
        $shifts=EntityHelper::getTableKVListWithSortOption($this->adapter,ShiftSetup::TABLE_NAME,ShiftSetup::SHIFT_ID,[ShiftSetup::SHIFT_ENAME],[ShiftSetup::STATUS=>'E'], ShiftSetup::SHIFT_ENAME,"ASC",NULL,FALSE,TRUE);
        $shiftFormElement = new Select();
        $shiftFormElement->setName("shift");
        $shiftFormElement->setValueOptions($shifts);
        $shiftFormElement->setAttributes(["id" => "shiftId", "class" => "form-control"]);
        $shiftFormElement->setLabel("Shift");

        return new ViewModel([
            'shiftFormElement'=>$shiftFormElement,
            'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }


}