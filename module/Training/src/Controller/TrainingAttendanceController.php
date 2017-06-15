<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Training\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Training\Model\TrainingAttendance;
use Training\Repository\TrainingAttendanceRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Description of TrainingAttendanceController
 *
 * @author root
 */
class TrainingAttendanceController extends AbstractActionController {

    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new TrainingAttendanceRepository($adapter);
    }

    public function indexAction() {
        $list = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['list' => $list]);
    }

    public function attendanceAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('trainingAtt');
        }

        $list = $this->repository->fetchTrainingAssignedEmp($id);
        return Helper::addFlashMessagesToArray($this, ['list' => $list,'trainingId'=>$id]);
    }

    public function updateTrainingAtdAction() {

        $request = $this->getRequest();
        $postData = $request->getPost();
        
        $trainingAttendance= new TrainingAttendance();
        
        $trainingAttendance->employeeId=$postData['employeeId'];
        $trainingAttendance->trainingId=$postData['trainingId'];
        $trainingAttendance->trainingDt=$postData['trainingDate'];
        $trainingAttendance->attendanceStatus=$postData['attendanceStatus'];
        
        $this->repository->updateTrainingAtd($trainingAttendance);
        
        return new CustomViewModel([
            'success' => true,
            'data' => $trainingAttendance
        ]);
    }

}
