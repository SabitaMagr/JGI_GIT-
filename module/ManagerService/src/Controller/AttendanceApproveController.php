<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/4/16
 * Time: 5:05 PM
 */

namespace ManagerService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\ViewModel;
use ManagerService\Repository\AttendanceApproveRepository;
use Application\Helper\Helper;
use SelfService\Form\AttendanceRequestForm;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Model\AttendanceRequestModel;
use AttendanceManagement\Model\AttendanceByHr;
use AttendanceManagement\Repository\AttendanceByHrRepository;

class AttendanceApproveController extends AbstractActionController {

    private $repository;
    private $adapter;
    private $employeeId;
    private $userId;
    private $authService;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new AttendanceApproveRepository($adapter);

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->userId = $recordDetail['user_id'];
        $this->employeeId = $recordDetail['employee_id'];
    }

    public function initializeForm(){
        $attendanceRequestForm = new AttendanceRequestForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($attendanceRequestForm);
    }

    public function indexAction()
    {
        $list = $this->repository->getAllRequest($this->employeeId,'RQ');
        return Helper::addFlashMessagesToArray($this, ['list' => $list]);
    }

    public function viewAction(){
        $this->initializeForm();
        $id = (int)$this->params()->fromRoute('id');

        if($id===0){
            return $this->redirect()->toRoute("attedanceapprove");
        }

        $request = $this->getRequest();
        $model = new AttendanceRequestModel();
        $detail = $this->repository->fetchById($id);
        $employeeId = $detail['EMPLOYEE_ID'];
        $employeeName = $detail['FIRST_NAME']." ".$detail['MIDDLE_NAME']." ".$detail['LAST_NAME'];

        $attendanceDetail = new AttendanceByHr();
        $attendanceRepository = new AttendanceByHrRepository($this->adapter);

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
                $attendanceDetail->id = (int)Helper::getMaxId($this->adapter,AttendanceByHr::TABLE_NAME,AttendanceByHr::ID)+1;

                $employeeId = $detail['EMPLOYEE_ID'];
                $attendanceDt = $detail['ATTENDANCE_DT'];

                $previousDtl = $attendanceRepository->getDtlWidEmpIdDate($employeeId,$attendanceDt);

                if($previousDtl==null){
                    $attendanceRepository->add($attendanceDetail);
                }else{
                    $attendanceRepository->edit($attendanceDetail,$previousDtl['ID']);
                }

                $this->flashmessenger()->addMessage("Attendance Request Approved!!!");

            }else if($action=="Reject"){
                $model->status="R";
                $this->flashmessenger()->addMessage("Attendance Request Rejected!!!");
            }
            $model->approvedRemarks = $reason;
            $this->repository->edit($model,$id);
            return $this->redirect()->toRoute("attedanceapprove");
        }
        return Helper::addFlashMessagesToArray($this,[
            'form'=>$this->form,
            'id'=>$id,
            'status'=>$detail['STATUS'],
            'employeeName'=>$employeeName,
            'employeeId'=>$employeeId,
            'requestedDt'=>$detail['REQUESTED_DT'],
        ]);
    }
    public function statusAction(){
        $pendingList = $this->repository->getAllRequest($this->employeeId,'RQ');
        $approvedList = $this->repository->getAllRequest($this->employeeId,'AP');
        $rejectedList = $this->repository->getAllRequest($this->employeeId,'R');
        return Helper::addFlashMessagesToArray($this,[
            'pendingList'=>$pendingList,
            'approvedList'=>$approvedList,
            'rejectedList'=>$rejectedList
        ]);
    }
}