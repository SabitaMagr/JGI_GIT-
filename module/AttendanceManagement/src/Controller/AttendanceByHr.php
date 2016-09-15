<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/14/16
 * Time: 3:29 PM
 */
namespace  AttendanceManagement\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use AttendanceManagement\Form\AttendanceByHrForm;
use AttendanceManagement\Repository\AttendanceByHrRepository;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Setup\Helper\EntityHelper;
use AttendanceManagement\Model\AttendanceByHr as AttendanceByHrModel;

class AttendanceByHr extends  AbstractActionController {

    private $adapter;
    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new AttendanceByHrRepository($adapter);
    }

    public function initializeForm()
    {
        $builder = new AnnotationBuilder();
        $attendanceByHr = new AttendanceByHrForm();
        $this->form = $builder->createForm($attendanceByHr);
    }

    public function indexAction()
    {
        $attendanceList = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['attendanceList' => $attendanceList]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();
        if($request->isPost()){
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
                $attendanceByHrModel = new AttendanceByHrModel();
                $attendanceByHrModel->exchangeArrayFromForm($this->form->getData());
                $attendanceByHrModel->attendanceDt = Helper::getExpressionDate($attendanceByHrModel->attendanceDt);
                $attendanceByHrModel->id=((int)Helper::getMaxId($this->adapter, AttendanceByHrModel::TABLE_NAME, "ID")) + 1;
                $attendanceByHrModel->inTime = Helper::getExpressionTime($attendanceByHrModel->inTime);
                $attendanceByHrModel->outTime = Helper::getExpressionTime($attendanceByHrModel->outTime);
                $this->repository->add($attendanceByHrModel);
                $this->flashmessenger()->addMessage("Attendance Submitted Successfully!!");
                return $this->redirect()->toRoute("attendancebyhr");
            }
        }
        return Helper::addFlashMessagesToArray($this,
            [
                'form' => $this->form,
                'employees' => \Application\Helper\EntityHelper::getTableKVList($this->adapter,"HR_EMPLOYEES","EMPLOYEE_ID",["FIRST_NAME","MIDDLE_NAME","LAST_NAME"])
            ]
        );

    }

    public function editAction(){
        $this->initializeForm();
        $id = (int)$this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("attendancebyhr");
        }

        $request = $this->getRequest();
        $attendanceByHrModel = new AttendanceByHrModel();
        if(!$request->isPost()){
            $attendanceByHrModel->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($attendanceByHrModel);
        }else{
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $attendanceByHrModel->exchangeArrayFromForm($this->form->getData());
                $attendanceByHrModel->attendanceDt = Helper::getExpressionDate($attendanceByHrModel->attendanceDt);
                $attendanceByHrModel->inTime = Helper::getExpressionTime($attendanceByHrModel->inTime);
                $attendanceByHrModel->outTime = Helper::getExpressionTime($attendanceByHrModel->outTime);
                $this->repository->edit($attendanceByHrModel, $id);
                $this->flashmessenger()->addMessage("Attendance Updated Successfully!!");
                return $this->redirect()->toRoute("attendancebyhr");
            }
        }
        return Helper::addFlashMessagesToArray($this,
            [
                'form' => $this->form,
                'id'=>$id,
                'employees' => \Application\Helper\EntityHelper::getTableKVList($this->adapter,"HR_EMPLOYEES","EMPLOYEE_ID",["FIRST_NAME","MIDDLE_NAME","LAST_NAME"])
            ]
        );
    }

    public function deleteAction(){

    }
}