<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 7/29/16
 * Time: 11:02 AM
 */

namespace Setup\Controller;


use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Setup\Model\Employee;


class EmployeeController extends AbstractActionController
{
    protected $form;
    private $db;

    public function __construct(AdapterInterface $db)
    {
        $this->db = $db;

    }


    public function addAction()
    {
        $employee = new Employee();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($employee);
        }

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return new ViewModel([
                'form' => $this->form
            ]);

        }


        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
        $employee->exchangeArray($this->form->getData());

        $table=new TableGateway('employee',$this->db);
        $table->insert($employee->getArrayCopy());
            return $this->redirect()->toRoute("sdf");

        }else{
            return $this->redirect()->toRoute("123");

        }


    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('setup', ['action' => 'index']);
        }


        $employee = new Employee();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($employee);
        }


        $employeeTable=new TableGateway('employee',$this->db);


        $request = $this->getRequest();
        $viewData = [];

        if (! $request->isPost()) {
        $rowset      = $employeeTable->select(['employeeCode' => $id]);
        $artistRow   = $rowset->current();

        $this->form->bind($artistRow);

        $viewData = ['id' => $id, 'form' => $this->form];
            return $viewData;
        }

        $this->form->setData($request->getPost());

        if(!$this->form->isValid()){
            return $viewData;
        }

        $employee->exchangeArray($this->form->getData());
        $employeeTable->update($employee->getArrayCopy(),['employeeCode'=>$id]);


        return $this->redirect()->toRoute("setup");

    }

    public function indexAction(){
        $employeeTable=new TableGateway('employee',$this->db);

        $rowset=$employeeTable->select();

        return new ViewModel(['list'=>$rowset]);




    }


}