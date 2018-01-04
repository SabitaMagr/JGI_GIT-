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

class AttendanceDeviceController extends AbstractActionController {

    private $form;
    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new AttendanceDeviceRepository($adapter);
    }

    public function initializeForm() {
        $attendanceDeviceForm = new AttendanceDeviceForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($attendanceDeviceForm);
    }

    public function indexAction() {
        $list = $this->repository->fetchAll();
        $attendanceDevice = [];
        foreach ($list as $row) {
            $row['PING_STATUS']=$this->pingAddress($row['DEVICE_IP']);
            array_push($attendanceDevice, $row);
        }
        return Helper::addFlashMessagesToArray($this, ['attendanceDevice' => $attendanceDevice]);
    }

    public function addAction() {
        $allCompanyBranches = new DepartmentRepository($this->adapter);
        $this->initializeForm();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $attendanceDevice = new AttendanceDevice();
                $attendanceDevice->exchangeArrayFromForm($this->form->getData());
                $attendanceDevice->deviceId = ((int) Helper::getMaxId($this->adapter, AttendanceDevice::TABLE_NAME, AttendanceDevice::DEVICE_ID)) + 1;
                $attendanceDevice->status = 'E';
                $attendanceDevice->branchId = $attendanceDevice->deviceId;
                $this->repository->add($attendanceDevice);

                $this->flashmessenger()->addMessage("Attendance Device Successfully Added!!!");
                return $this->redirect()->toRoute("AttendanceDevice");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
//                    'company' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], "COMPANY_NAME", "ASC", null, false, true),
//                    'branch' => $allCompanyBranches->fetchAllBranchAndCompany(),
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $allCompanyBranches = new DepartmentRepository($this->adapter);
        $this->initializeForm();
        $request = $this->getRequest();

        $attendanceDevice = new AttendanceDevice();
        $detail = $this->repository->fetchById($id)->getArrayCopy();

        if (!$request->isPost()) {
            $attendanceDevice->exchangeArrayFromDB($detail);
            $this->form->bind($attendanceDevice);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $attendanceDevice->exchangeArrayFromForm($this->form->getData());
                $this->repository->edit($attendanceDevice, $id);
                $this->flashmessenger()->addMessage("Attendance Device Successfully Updated!!!");
                return $this->redirect()->toRoute("AttendanceDevice");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'form' => $this->form,
//                    'company' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], "COMPANY_NAME", "ASC", null, false, true),
//                    'branch' => $allCompanyBranches->fetchAllBranchAndCompany(),
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('usersetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Attendnace Device Successfully Deleted!!!");
        return $this->redirect()->toRoute('AttendanceDevice');
    }
    
    function pingAddress($ip) {
        $output = shell_exec('ping -n 1 ' . $ip);
        if (strpos($output, 'Destination host unreachable') !== false) {
            $result="Destination host unreachable";
        } elseif (strpos($output, 'Request timed out') !== false) {
            $result="Request timed out";
        } elseif (strpos($output, 'Expired') !== false) {
            $result="Expired in Transit";
        } elseif (strpos($output, 'data') !== false) {
            $result="ONLINE";
        } else {
            $result="Unknown Error";
        }
        return $result;
    }

}
