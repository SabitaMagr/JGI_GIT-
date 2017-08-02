<?php

namespace Application\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Application\Repository\ApiRepository;
use Exception;
use Setup\Model\HrEmployees;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractRestfulController;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ApiController extends AbstractRestfulController {

    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new ApiRepository($adapter);
    }

    public function indexAction() {
        try {
//            throw new Exception("test");
            $request = $this->getRequest();
            $requestType = $request->getMethod();
            $data = [];

            switch ($requestType) {
                case Request::METHOD_POST:
                    $postData = $request->getPost();
                    $data = $this->addEmployee($postData->getArrayCopy());
//                    http_response_code(201);
                    break;

                case Request::METHOD_GET:
                    $id = $this->params()->fromRoute('id');
                    $data = $this->fetchemployeeList($id);
                    break;

                case Request::METHOD_PUT:
                    $id = $this->params()->fromRoute('id');
                    if ($id == 0 || $id == NULL) {
                        throw new Exception('id cannot be null or zero');
                    }
                    $editData = array();
                    parse_str($request->getContent(), $editData);
                    $data = $this->editEmployee($editData, $id);
                    break;

                case Request::METHOD_DELETE:
                    $id = $this->params()->fromRoute('id');
                    if ($id == 0 || $id == NULL) {
                        throw new Exception('id cannot be null or zero');
                    }
                    $data = $this->deleteEmployee($id);
                    break;
            }
            return new CustomViewModel($data);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'error' => $e->getMessage()]);
//            return new CustomViewModel($e->getMessage());
        }
    }
    
    
    public function setupAction(){
        $name = $this->params()->fromRoute('name');
        
        
        
        
        
        
    }

    public function fetchemployeeList($id) {
        $data = $this->repository->fetchAllEmployee($id);
        return $data;
    }

//    public function 

    public function addEmployee($postData) {

        $employeeModel = new HrEmployees();

        $employeeModel->exchangeArrayFromDB($postData);

        $employeeModel->employeeId = ((int) Helper::getMaxId($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID")) + 1;
        $employeeModel->status = 'E';
        $employeeModel->createdDt = Helper::getcurrentExpressionDate();
//        $employeeModel->addrPermCountryId = 168;
//        $employeeModel->addrTempCountryId = 168;

        $returnData = $this->repository->add($employeeModel);

        return $returnData;
    }

    public function deleteEmployee($id) {
        $returnData = $this->repository->delete($id);
        return $returnData;
    }

    public function editEmployee($editData, $id) {

        $employeeModel = new HrEmployees();
        $employeeModel->exchangeArrayFromDB($editData);
        $employeeModel->modifiedDt = Helper::getcurrentExpressionDate();
        $returnData = $this->repository->edit($employeeModel, $id);
        return $returnData;
    }

}
