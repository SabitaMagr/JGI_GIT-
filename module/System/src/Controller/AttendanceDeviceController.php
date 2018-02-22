<?php

namespace System\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Exception;
use Setup\Repository\DepartmentRepository;
use System\Form\AttendanceDeviceForm;
use System\Model\AttendanceDevice;
use System\Repository\AttendanceDeviceRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class AttendanceDeviceController extends HrisController {

//    private $form;
//    private $adapter;
//    private $repository;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(AttendanceDeviceRepository::class);
        $this->initializeForm(AttendanceDeviceForm::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $list = $this->repository->fetchAll();
                $attendanceDevice = [];
                foreach ($list as $row) {
                    $row['PING_STATUS'] = '---';
                    array_push($attendanceDevice, $row);
                }
                return new JsonModel(['success' => true, 'data' => $attendanceDevice, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return $this->stickFlashMessagesTo([
                    'acl' => $this->acl
        ]);
    }

    public function addAction() {
        $allCompanyBranches = new DepartmentRepository($this->adapter);

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
//        $allCompanyBranches = new DepartmentRepository($this->adapter);

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
            $result = "Destination host unreachable";
        } elseif (strpos($output, 'Request timed out') !== false) {
            $result = "Request timed out";
        } elseif (strpos($output, 'Expired') !== false) {
            $result = "Expired in Transit";
        } elseif (strpos($output, 'data') !== false) {
//            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//            $connection = @socket_connect($socket, $ip, 23);
//            if ($connection) {
//                $result = 'ONLINE';
//            } else {
//                $result = socket_strerror(socket_last_error($socket));
//            }
//            socket_close($socket);
            $result = "ONLINE";
        } else {
            $result = "Unknown Error";
        }
        return $result;
    }

    public function pullDeviceWithPingStatusAction() {
        try {
            $list = $this->repository->fetchAll();
            $attendanceDevice = [];
            foreach ($list as $row) {
                $row['PING_STATUS'] = $this->pingAddress($row['DEVICE_IP']);
                array_push($attendanceDevice, $row);
            }
            return new JsonModel(['success' => true, 'data' => $attendanceDevice, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
