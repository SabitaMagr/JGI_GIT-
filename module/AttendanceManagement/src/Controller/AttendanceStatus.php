<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/25/16
 * Time: 11:57 AM
 */
namespace AttendanceManagement\Controller;

use Application\Helper\Helper;
use AttendanceManagement\Repository\AttendanceStatusRepository;
use SelfService\Repository\AttendanceRequestRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Form\AttendanceRequestForm;
use SelfService\Model\AttendanceRequestModel;
use Zend\Form\Annotation\AnnotationBuilder;
use AttendanceManagement\Model\AttendanceDetail;
use AttendanceManagement\Repository\AttendanceDetailRepository;

class AttendanceStatus extends AbstractActionController {

    private $adapter;
    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new AttendanceStatusRepository($adapter);
    }

    public function initializeForm(){
        $attendanceRequestForm = new AttendanceRequestForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($attendanceRequestForm);
    }

    public function indexAction()
    {
        $pendingList = $this->repository->getAllRequest('RQ');
        $approvedList = $this->repository->getAllRequest('AP');
        $rejectedList = $this->repository->getAllRequest('R');
        return Helper::addFlashMessagesToArray($this,[
            'pendingList'=>$pendingList,
            'approvedList'=>$approvedList,
            'rejectedList'=>$rejectedList
        ]);
    }

    public function viewAction(){
        $this->initializeForm();
        $id = (int)$this->params()->fromRoute('id');

        if($id===0){
            return $this->redirect()->toRoute("attendancestatus");
        }

        $request = $this->getRequest();
        $model = new AttendanceRequestModel();
        $detail = $this->repository->fetchById($id);
        $employeeName = $detail['FIRST_NAME']." ".$detail['MIDDLE_NAME']." ".$detail['LAST_NAME'];
        $approver = $detail['FIRST_NAME1']." ".$detail['MIDDLE_NAME1']." ".$detail['LAST_NAME1'];
        $status = $detail['STATUS'];

        $attendanceDetail = new AttendanceDetail();
        $attendanceRepository = new AttendanceDetailRepository($this->adapter);
        $attendanceRequestRepository = new AttendanceRequestRepository($this->adapter);

        if (!$request->isPost()) {
            $model->exchangeArrayFromDB($detail);
            $this->form->bind($model);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $model->approvedDt=Helper::getcurrentExpressionDate();

            if($action=="Approve"){
                $model->status="AP";
                $attendanceDetail->attendanceDt=Helper::getcurrentExpressionDate($detail['ATTENDANCE_DT']);
                $attendanceDetail->inTime = Helper::getExpressionTime($detail['IN_TIME']);
                $attendanceDetail->inRemarks = $detail['IN_REMARKS'];
                $attendanceDetail->outTime =  Helper::getExpressionTime($detail['OUT_TIME']);
                $attendanceDetail->outRemarks = $detail['OUT_REMARKS'];
                $attendanceDetail->totalHour = $detail['TOTAL_HOUR'];
                $attendanceDetail->employeeId = $detail['EMPLOYEE_ID'];
                $attendanceDetail->id = (int)Helper::getMaxId($this->adapter,AttendanceDetail::TABLE_NAME,AttendanceDetail::ID)+1;
                $attendanceRepository->add($attendanceDetail);

                $this->flashmessenger()->addMessage("Attendance Request Approved!!!");

            }else if($action=="Reject"){
                $model->status="R";
                $this->flashmessenger()->addMessage("Attendance Request Rejected!!!");
            }
            $model->approvedRemarks=$reason;
            $attendanceRequestRepository->edit($model,$id);
            return $this->redirect()->toRoute("attendancestatus");
        }
        return Helper::addFlashMessagesToArray($this,[
            'form'=>$this->form,
            'id'=>$id,
            'employeeName'=>$employeeName,
            'approver'=>$approver,
            'status'=>$status,
            'requestedDt'=>$detail['REQUESTED_DT'],
        ]);
    }
}