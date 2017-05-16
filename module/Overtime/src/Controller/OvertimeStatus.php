<?php
namespace Overtime\Controller;

use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Overtime\Repository\OvertimeStatusRepository;
use ManagerService\Repository\OvertimeApproveRepository;
use SelfService\Repository\OvertimeDetailRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Form\OvertimeRequestForm;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Model\Overtime;
use Zend\Form\Element\Select;
use Zend\Authentication\AuthenticationService;
use Setup\Repository\RecommendApproveRepository;
use System\Model\PreferenceSetup;
use System\Repository\PreferenceSetupRepo;
use Setup\Repository\EmployeeRepository;
use AttendanceManagement\Repository\AttendanceRepository;
use SelfService\Model\OvertimeDetail;
use SelfService\Repository\OvertimeRepository;

class OvertimeStatus extends AbstractActionController
{
    private $adapter;
    private $overtimeApproveRepository;
    private $overtimeStatusRepository;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->overtimeApproveRepository = new OvertimeApproveRepository($adapter);
        $this->overtimeStatusRepository = new OvertimeStatusRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId =  $auth->getStorage()->read()['employee_id'];       
    }
    
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new OvertimeRequestForm();
        $this->form = $builder->createForm($form);
    }

    
    public function indexAction() {
        $status = [
            '-1' => 'All',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected',
            'C'=>'Cancelled'
        ];
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "requestStatusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'status' => $statusFormElement,
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }
    public function viewAction() {
        $this->initializeForm();

        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("overtimeStatus");
        }
        $overtimeModel = new Overtime();
        $request = $this->getRequest();

        $detail = $this->overtimeApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $employeeId = $detail['EMPLOYEE_ID'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);
        $recommApprove = 0;
        if($empRecommendApprove['RECOMMEND_BY']==$empRecommendApprove['APPROVED_BY']){
            $recommApprove=1;
        }
        
        $employeeName = $detail['FIRST_NAME'] . " " . $detail['MIDDLE_NAME'] . " " . $detail['LAST_NAME'];        
        $RECM_MN = ($detail['RECM_MN']!=null)? " ".$detail['RECM_MN']." ":" ";
        $recommender = $detail['RECM_FN'].$RECM_MN.$detail['RECM_LN'];        
        $APRV_MN = ($detail['APRV_MN']!=null)? " ".$detail['APRV_MN']." ":" ";
        $approver = $detail['APRV_FN'].$APRV_MN.$detail['APRV_LN'];
        $MN1 = ($detail['MN1']!=null)? " ".$detail['MN1']." ":" ";
        $recommended_by = $detail['FN1'].$MN1.$detail['LN1'];        
        $MN2 = ($detail['MN2']!=null)? " ".$detail['MN2']." ":" ";
        $approved_by = $detail['FN2'].$MN2.$detail['LN2'];
        $authRecommender = ($status=='RQ' || $status=='C')?$recommender:$recommended_by;
        $authApprover = ($status=='RC' || $status=='C' || $status=='RQ' || ($status=='R' && $approvedDT==null))?$approver:$approved_by;


        if (!$request->isPost()) {
            $overtimeModel->exchangeArrayFromDB($detail);
            $this->form->bind($overtimeModel);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $overtimeModel->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $overtimeModel->status = "R";
                $this->flashmessenger()->addMessage("Overtime Request Rejected!!!");
            } else if ($action == "Approve") {
                $overtimeModel->status = "AP";
                $this->flashmessenger()->addMessage("Overtime Request Approved");
            }
            $overtimeModel->approvedBy = $this->employeeId;
            $overtimeModel->approvedRemarks = $reason;
            $this->overtimeApproveRepository->edit($overtimeModel, $id);

            return $this->redirect()->toRoute("overtimeStatus");
        }
        $overtimeDetailRepo = new OvertimeDetailRepository($this->adapter);
        $overtimeDetailResult =$overtimeDetailRepo->fetchByOvertimeId($detail['OVERTIME_ID']);
        $overtimeDetails = [];
        foreach($overtimeDetailResult as $overtimeDetailRow){
            array_push($overtimeDetails,$overtimeDetailRow);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeId' => $employeeId,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DATE'],
                    'recommender' => $authRecommender,
                    'approvedDT'=>$detail['APPROVED_DATE'],
                    'approver' => $authApprover,
                    'status' => $status,
                    'customRenderer' => Helper::renderCustomView(),
                    'recommApprove'=>$recommApprove,
                    'overtimeDetails'=>$overtimeDetails,
                    'totalHour'=>$detail['TOTAL_HOUR']
        ]);
    }
    
    public function calculateAction(){
        $preferenceSetupRepo = new PreferenceSetupRepo($this->adapter);
        $employeeRepo = new EmployeeRepository($this->adapter);
        $overtimeModel = new Overtime();
        $overtimeRepository = new OvertimeRepository($this->adapter);
        $overtimeDetailModel = new OvertimeDetail();
        $overtimeDetailRepo = new OvertimeDetailRepository($this->adapter);
        $overtimeRequestSetting = $preferenceSetupRepo->fetchByPreferenceName("OVERTIME_REQUEST");
        $employeeAdmin = $employeeRepo->fetchByAdminFlag();
        foreach($overtimeRequestSetting as $overtimeRequestSettingRow){
//            $attendanceDt = date(Helper::PHP_DATE_FORMAT);
            $attendanceDt = "10-May-2017";
            $employeeResult = $employeeRepo->fetchByEmployeeTypeWidShift($overtimeRequestSettingRow['EMPLOYEE_TYPE'],$attendanceDt);
            $preferenceConstraint = $overtimeRequestSettingRow['PREFERENCE_CONSTRAINT'];
            $preferenceCondition = $overtimeRequestSettingRow['PREFERENCE_CONDITION'];
            $constraintValue = $overtimeRequestSettingRow['CONSTRAINT_VALUE'];
            $constraintType = $overtimeRequestSettingRow['CONSTRAINT_TYPE'];
            $requestType = $overtimeRequestSettingRow['REQUEST_TYPE'];
            foreach($employeeResult as $employeeRow){
                if($preferenceConstraint=='OVERTIME_GRACE_TIME' && $constraintType=='HOUR'){
                    $attendanceRepository = new AttendanceRepository($this->adapter);
                    $attendanceResult = $attendanceRepository->fetchAllByEmpIdAttendanceDt($employeeRow['EMPLOYEE_ID'],$attendanceDt);
                    $attendanceNum = count($attendanceResult);
                    if($attendanceNum!=0 && $attendanceNum%2==0){
                        $getTotalHourTime = $attendanceRepository->getTotalByEmpIdAttendanceDt($employeeRow['EMPLOYEE_ID'], $attendanceDt);
                        $shiftTotalWorkingHrMin = Helper::hoursToMinutes($employeeRow['TOTAL_WORKING_HR']);
                        $lateInHrMin =  Helper::hoursToMinutes($employeeRow['LATE_IN']);
                        $earlyOutHrMin =  Helper::hoursToMinutes($employeeRow['EARLY_OUT']);
                        $actualWorkingHrMin = Helper::hoursToMinutes($employeeRow['ACTUAL_WORKING_HR']);
                        $actualBreakTime = $shiftTotalWorkingHrMin-$actualWorkingHrMin;
                        $totalWorkingHrMin = $getTotalHourTime['WORKING']['TOTAL_MINS'];
                        $totalNonWorkingHrMin = $getTotalHourTime['NON-WORKING']['TOTAL_MINS'];
                        if($totalWorkingHrMin>$actualWorkingHrMin){
                            $extraOvertime = ($actualBreakTime>$totalNonWorkingHrMin)?$actualBreakTime-$totalNonWorkingHrMin:0;
                            $overtime = ($totalWorkingHrMin - $actualWorkingHrMin)-$extraOvertime;  
                            $overtimeHr = Helper::minutesToHours($overtime);
                            $constraintValueMin = Helper::hoursToMinutes($constraintValue);
                            $overtimeModel->overtimeId = ((int) Helper::getMaxId($this->adapter, Overtime::TABLE_NAME, Overtime::OVERTIME_ID)) + 1;
                            $overtimeModel->employeeId = $employeeRow['EMPLOYEE_ID'];
                            $overtimeModel->overtimeDate = Helper::getExpressionDate($attendanceDt);
                            $overtimeModel->requestedDate = Helper::getcurrentExpressionDate();
                            $overtimeModel->description = "Overtime Request";
                            $overtimeModel->allTotalHour = Helper::getExpressionTime($overtimeHr, Helper::ORACLE_TIMESTAMP_FORMAT);
                            $overtimeModel->status = $requestType;
                            if($requestType=='AP'){
                                $overtimeModel->recommendedBy = $employeeAdmin['EMPLOYEE_ID'];
                                $overtimeModel->approvedBy = $employeeAdmin['EMPLOYEE_ID'];
                            }
                            $inTime = strtotime($employeeRow['IN_TIME']);
                            $shiftStartTime = strtotime($employeeRow['START_TIME']);
                            $outTime = strtotime($employeeRow['OUT_TIME']);
                            $shiftEndTime = strtotime($employeeRow['END_TIME']);
                            $result=0;
                            if($preferenceCondition=="LESS_THAN"){
                                if($overtime<$constraintValueMin){
                                    $result = $overtimeRepository->add($overtimeModel);
                                }
                            }else if($preferenceCondition=="GREATER_THAN"){
                                if($overtime>$constraintValueMin){
                                    $result =$overtimeRepository->add($overtimeModel);
                                }
                            }else if($preferenceCondition=='EQUAL'){
                                if($overtime==$constraintValueMin){
                                    $result =$overtimeRepository->add($overtimeModel);
                                }
                            }
                            if($result==1){
                                if($inTime!=$shiftStartTime && $inTime<$shiftStartTime){
                                    $dtlTotalHr = Helper::minutesToHours(round(abs($shiftStartTime - $inTime) / 60,2));
                                    $overtimeDetailModel->overtimeId = $overtimeModel->overtimeId;
                                    $overtimeDetailModel->detailId = ((int) Helper::getMaxId($this->adapter, OvertimeDetail::TABLE_NAME, OvertimeDetail::DETAIL_ID)) + 1;
                                    $overtimeDetailModel->startTime = Helper::getExpressionTime($employeeRow['IN_TIME']);
                                    $overtimeDetailModel->endTime = Helper::getExpressionTime($employeeRow['START_TIME']);
                                    $overtimeDetailModel->status = 'E';
                                    $overtimeDetailModel->createdBy = $this->employeeId;
                                    $overtimeDetailModel->totalHour =Helper::getExpressionTime($dtlTotalHr, Helper::ORACLE_TIMESTAMP_FORMAT);
                                    $overtimeDetailModel->createdDate =  Helper::getcurrentExpressionDate();
                                    $overtimeDetailRepo->add($overtimeDetailModel);
                                }
                                if($outTime!=$shiftEndTime && $shiftEndTime<$outTime){
                                    $dtlTotalHr = Helper::minutesToHours(round(abs($outTime - $shiftEndTime) / 60,2));
                                    $overtimeDetailModel->overtimeId = $overtimeModel->overtimeId;
                                    $overtimeDetailModel->detailId = ((int) Helper::getMaxId($this->adapter, OvertimeDetail::TABLE_NAME, OvertimeDetail::DETAIL_ID)) + 1;
                                    $overtimeDetailModel->startTime = Helper::getExpressionTime($employeeRow['END_TIME']);
                                    $overtimeDetailModel->endTime = Helper::getExpressionTime($employeeRow['OUT_TIME']);
                                    $overtimeDetailModel->status = 'E';
                                    $overtimeDetailModel->totalHour =Helper::getExpressionTime($dtlTotalHr, Helper::ORACLE_TIMESTAMP_FORMAT);
                                    $overtimeDetailModel->createdBy = $this->employeeId;
                                    $overtimeDetailModel->createdDate =  Helper::getcurrentExpressionDate();
                                    $overtimeDetailRepo->add($overtimeDetailModel);
                                }
                                $this->flashmessenger()->addMessage("Overtime Request Successfully Generated!!!");
                            }else{
                                $this->flashmessenger()->addMessage("There is no required data to generate overtime request!!!");
                            }
                        }
                    }
                }
            }
        }
        $this->redirect()->toRoute('overtimeStatus');
    }
}