<?php

namespace Training\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Training\Model\TrainingAttendance;
use Training\Repository\TrainingAttendanceRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

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
        print "<pre>";
        print_r($list);
        exit;
        return Helper::addFlashMessagesToArray($this, ['list' => $list, 'trainingId' => $id]);
    }

    public function updateTrainingAtdAction() {

        $request = $this->getRequest();
        $postData = $request->getPost();

        $trainingAttendance = new TrainingAttendance();

        $trainingAttendance->employeeId = $postData['employeeId'];
        $trainingAttendance->trainingId = $postData['trainingId'];
        $trainingAttendance->trainingDt = $postData['trainingDate'];
        $trainingAttendance->attendanceStatus = $postData['attendanceStatus'];

        $this->repository->updateTrainingAtd($trainingAttendance);

        return new CustomViewModel([
            'success' => true,
            'data' => $trainingAttendance
        ]);
    }

}
