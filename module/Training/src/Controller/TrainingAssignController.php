<?php

namespace Training\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\HrEmployees;
use Setup\Model\Position;
use Setup\Model\ServiceEventType;
use Setup\Model\ServiceType;
use Setup\Model\Training;
use Training\Form\TrainingAssignForm;
use Training\Repository\TrainingAssignRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class TrainingAssignController extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new TrainingAssignRepository($this->adapter);
    }

    public function indexAction() {
        
        $trainingFormElement = new Select();
        $trainingFormElement->setName("training");
        $trainings = EntityHelper::getTableKVListWithSortOption($this->adapter, Training::TABLE_NAME, Training::TRAINING_ID, [Training::TRAINING_NAME], [Training::STATUS => 'E'], "TRAINING_NAME", "ASC",null,false,true);
        $trainings1 = [-1 => "All Training"] + $trainings;
        $trainingFormElement->setValueOptions($trainings1);
        $trainingFormElement->setAttributes(["id" => "trainingId", "class" => "form-control"]);
        $trainingFormElement->setLabel("Training");

        return Helper::addFlashMessagesToArray($this, [
                    'list' => 'list',
                    'trainings' => $trainingFormElement,
                    'searchValues'=> EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new TrainingAssignForm();
        $this->form = $builder->createForm($form);
    }

    public function addAction() {
        $this->initializeForm();
        $employee = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => 'E'], " ");
        $trainingList = array(
            '1' => 'Organizational Hard Skills',
            '2' => 'Organizational',
            '3' => 'Organizational Soft Skills'
        );
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employees' => $employee,
                    'training' => $trainingList
        ]);
    }

    public function assignAction() {
        $trainingFormElement = new Select();
        $trainingFormElement->setName("training");
        $trainings = EntityHelper::getTableKVListWithSortOption($this->adapter, Training::TABLE_NAME, Training::TRAINING_ID, [Training::TRAINING_NAME], [Training::STATUS => 'E'], "TRAINING_NAME", "ASC",null,false,true);
        $trainingFormElement->setValueOptions($trainings);
        $trainingFormElement->setAttributes(["id" => "trainingId", "class" => "form-control"]);
        $trainingFormElement->setLabel("Training");

        return Helper::addFlashMessagesToArray($this, [
                    'list' => 'list',
                    'searchValues'=> EntityHelper::getSearchData($this->adapter),
                    'trainings' => $trainingFormElement
        ]);
    }

    public function deleteAction() {
        $employeeId = (int) $this->params()->fromRoute("employeeId");
        $trainingId = (int) $this->params()->fromRoute("trainingId");
        if (!$trainingId && !$employeeId) {
            return $this->redirect()->toRoute('trainingAssign');
        }
        //print_r("hellow"); die();
        $this->repository->delete([$employeeId, $trainingId]);
        $model = new \Training\Model\TrainingAssign();
        $model->trainingId = $trainingId;
        $model->employeeId = $employeeId;
        $this->flashmessenger()->addMessage("Training Assign Successfully Cancelled!!!");
        try {
            HeadNotification::pushNotification(NotificationEvents::TRAINING_CANCELLED, $model, $this->adapter, $this->plugin('url'));
        } catch (Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
        }
        return $this->redirect()->toRoute('trainingAssign');
    }

    public function viewAction() {
        $employeeId = (int) $this->params()->fromRoute("employeeId");
        $trainingId = (int) $this->params()->fromRoute("trainingId");

        if (!$employeeId && !$trainingId) {
            return $this->redirect()->toRoute('trainingAssign');
        }

        $detail = $this->repository->getDetailByEmployeeID($employeeId, $trainingId);

        return Helper::addFlashMessagesToArray($this, ['detail' => $detail]);
    }

}
