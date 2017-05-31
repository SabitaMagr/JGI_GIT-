<?php
namespace AttendanceManagement\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Zend\Form\Element\Select;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use Zend\Form\Annotation\AnnotationBuilder;
use AttendanceManagement\Form\AttendanceByHrForm;
use AttendanceManagement\Model\AttendanceDetail as AttendanceByHrModel;
use SelfService\Repository\OvertimeDetailRepository;
use SelfService\Repository\OvertimeRepository;
use Exception;

class CalculateOvertime extends AbstractActionController{
    private $adapter;
    private $repository;
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $this->repository = new AttendanceDetailRepository($adapter);
    }
    
    public function indexAction() {
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $status = array(
            "All" => "All Status",
            "P" => "Present Only",
            "A" => "Absent Only",
            "H" => "On Holiday",
            "L" => "On Leave"
        );
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setValue("P");
        $statusFormElement->setAttributes(["id" => "statusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");
        
        $employeeTypeFormElement = new Select();
        $employeeTypeFormElement->setName("employeeType");
        $employeeType = array(
            '-1'=>"All Employee Type",
            "C" => "Contract",
            "R" => "Regular"
        );
        $employeeTypeFormElement->setValueOptions($employeeType);
        $employeeTypeFormElement->setAttributes(["id" => "employeeTypeId", "class" => "form-control"]);
        $employeeTypeFormElement->setLabel("Employee Type");
        
        return Helper::addFlashMessagesToArray($this, [
                    'status' => $statusFormElement,
                    'employeeType'=>$employeeTypeFormElement,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }
    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $attendanceByHr = new AttendanceByHrForm();
        $this->form = $builder->createForm($attendanceByHr);
    }
    public function viewAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("calculateOvertime");
        }
        $attendanceByHrModel = new AttendanceByHrModel();
        $overtimeRepo = new OvertimeRepository($this->adapter);
        
        $detail = $this->repository->fetchById($id);
        $attendanceByHrModel->exchangeArrayFromDB($detail);
        $this->form->bind($attendanceByHrModel);
        $overtime = $overtimeRepo->getAllByEmployeeId($detail['EMPLOYEE_ID'],$detail['ATTENDANCE_DT'],'AP',true);
        $overtimeDetailRepo = new OvertimeDetailRepository($this->adapter);
        $overtimeDetailResult =$overtimeDetailRepo->fetchByOvertimeId($overtime['OVERTIME_ID']);
        $overtimeDetails = [];
        foreach($overtimeDetailResult as $overtimeDetailRow){
            array_push($overtimeDetails,$overtimeDetailRow);
        }
        
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'overtimeDetails'=>$overtimeDetails,
                    'overtimeInHour'=>$overtime['TOTAL_HOUR'],
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC",NULL,FALSE,TRUE)
                        ]
        );
    }
    public function calculateAction(){
        $request = $this->getRequest();
        $postData = $request->getPost()->getArrayCopy();
        $fromDate = $postData['fromDate'];
        $toDate = $postData['toDate'];
        $begin = new \DateTime($fromDate );
        $end = new \DateTime($toDate);
        try{
            $overtimeRepo = new OvertimeRepository($this->adapter);
            for($i = $begin; $i <= $end; $i->modify('+1 day')){
            $overtimeAutoCalc = $overtimeRepo->executeProcedure($i->format( "d-M-Y" ));
        }
            $this->flashmessenger()->addMessage("Calculation of Overtime Successfully Completed!!");
        }catch(Exception $e){
            $this->flashmessenger()->addMessage("Calculation of Overtime Failed!!");
            $this->flashmessenger()->addMessage($e->getMessage());
        }
        $this->redirect()->toRoute("calculateOvertime");
    }
    
}

