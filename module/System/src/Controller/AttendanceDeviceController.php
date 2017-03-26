<?php

namespace System\Controller;

use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Setup\Model\Company;
use Setup\Repository\DepartmentRepository;
use System\Form\AttendanceDeviceForm;
use System\Model\AttendanceDevice;
use System\Repository\AttendanceDeviceRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;



class AttendanceDeviceController extends AbstractActionController
{
    private $form;
    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
         $this->repository = new AttendanceDeviceRepository($adapter);
    }
    
    public function initializeForm(){
        $attendanceDeviceForm = new AttendanceDeviceForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($attendanceDeviceForm);
    }
    
    public function indexAction() {
           $list = $this->repository->fetchAll();
        $attendanceDevice = [];
        foreach($list as $row){
            array_push($attendanceDevice, $row);
        }
        return Helper::addFlashMessagesToArray($this, ['attendanceDevice' => $attendanceDevice]);

    }
    
    public function addAction(){
        $allCompanyBranches= new DepartmentRepository($this->adapter);
//        print_r($allCompanyBranches->fetchAllBranchAndCompany());
//        die();
        $this->initializeForm();
        
        $request=$this->getRequest();
        
        if($request->isPost()){
             $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $attendanceDevice = new AttendanceDevice();
                $attendanceDevice->exchangeArrayFromForm($this->form->getData());
                $attendanceDevice->deviceId = ((int)Helper::getMaxId($this->adapter, AttendanceDevice::TABLE_NAME, AttendanceDevice::DEVICE_ID)) + 1;
                $attendanceDevice->isActive='Y';

                $this->repository->add($attendanceDevice);

                $this->flashmessenger()->addMessage("Attendance Device Successfully Added!!!");
                return $this->redirect()->toRoute("AttendanceDevice");
            }
        }
       return Helper::addFlashMessagesToArray($this,[
           'form'=>$this->form,
           'company' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], "COMPANY_NAME", "ASC"),
           'branch' => $allCompanyBranches->fetchAllBranchAndCompany(),
        ]);
        
    }
}
