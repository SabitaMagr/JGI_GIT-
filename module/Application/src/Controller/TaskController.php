<?php

namespace Application\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Application\Model\TaskModel;
use Application\Repository\TaskRepository;
use Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class TaskController extends AbstractActionController {

    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
         $taskRepo = new TaskRepository($this->adapter);
                $result = $taskRepo->fetchEmployeeTask($this->employeeId);
                $list = [];
                foreach ($result as $row) {
                    $nrow['id']=$row['TASK_ID'];
                    $nrow['title']=$row['TASK_TITLE'];
                    $nrow['description']=$row['TASK_EDESC'];
                    $nrow['dueDate']=$row['END_DATE'];
                    $nrow['done']=false;
                    array_push($list, $nrow);
//                    array_push($list, $row);
                }
//                echo '<pre>';
//                print_r($list);
//                die();
        return [
        'list'=> $list
        ];
    }

    public function addAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $taskRepo = new TaskRepository($this->adapter);
                $taskModel = new TaskModel();
                $data = $this->getRequest()->getPost();
                $taskModel->taskId = ((int) Helper::getMaxId($this->adapter, $taskModel::TABLE_NAME, $taskModel::TASK_ID)) + 1;
                $taskModel->employeeId = $this->employeeId;
                $taskModel->taskTitle = $data->title;
//                $taskModel->taskTitle = $data->taskTitle;
                $taskModel->taskEdesc = $data->description;
//                $taskModel->taskEdesc = $data->taskEdesc;
                $taskModel->endDate =  Helper::getExpressionDate($data->dueDate);
//                $taskModel->endDate =  Helper::getExpressionDate($data->endDate);
                $taskModel->createdBy = $this->employeeId;
                $taskModel->approvedBy = $this->employeeId;
                $taskModel->approvedDate = Helper::getcurrentExpressionDate();
                
                $taskRepo->add($taskModel);
                $responseid=$taskModel->taskId;
                return new CustomViewModel(['success' => true, 'msg' => 'sucessfully added', 'data' => [$taskModel],'id'=>$responseid, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function fetchTaskAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $taskRepo = new TaskRepository($this->adapter);
                $result = $taskRepo->fetchEmployeeTask($this->employeeId);
                $list = [];
                foreach ($result as $row) {
                    $nrow['title']='title';
                    $nrow['description']=$row['TASK_EDESC'];
//                    $nrow['dueDate']=$row['END_DATE'];
                    $nrow['dueDate']='2015-01-31';
                    array_push($list, $nrow);
                }
                return new CustomViewModel(['success' => true, 'msg' => 'fetched', 'data' => $list, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function editAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $taskRepo = new TaskRepository($this->adapter);
                $data = $this->getRequest()->getPost();
                $id = $data->id;
                $result = $taskRepo->fetchById($id);
                print_r($result);
                die();
                return new CustomViewModel(['success' => true, 'msg' => 'edit', 'data' => $result, 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function updateAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $taskRepo = new TaskRepository($this->adapter);
                $taskModel = new TaskModel();
                $data = $this->getRequest()->getPost();
//                $id = $data->taskId;
                $id = $data->id;
                $taskModel->taskTitle = $data->title;
//                $taskModel->taskTitle = $data->taskTitle;
                $taskModel->taskEdesc = $data->description;
//                $taskModel->taskEdesc = $data->taskEdesc;
                $taskModel->endDate =  Helper::getExpressionDate($data->dueDate);
//                $taskModel->endDate =  Helper::getExpressionDate($data->endDate);
                $taskModel->modifiedBy = $this->employeeId;
                $taskModel->modifiedDt = Helper::getcurrentExpressionDate();
                $taskRepo->edit($taskModel, $id);
                return new CustomViewModel(['success' => true, 'msg' => 'sucessfully edited', 'data' => [$taskModel], 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
    
    
    public function deleteAction(){
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $taskRepo = new TaskRepository($this->adapter);
                $taskModel = new TaskModel();
                $data = $this->getRequest()->getPost();
//                                print_r($data);
//                return new CustomViewModel(['data'=>$data]);
//                die();
//                $id = $data->taskId;
                $id = $data->id;
                $taskRepo->delete($id);
                return new CustomViewModel(['success' => true, 'msg' => 'sucessfully delted', 'data' => [], 'error' => '']);
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
