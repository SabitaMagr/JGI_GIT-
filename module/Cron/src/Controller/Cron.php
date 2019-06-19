<?php

namespace Cron\Controller;

use Exception;
use Cron\Repository\CronRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Mail\Message;
use Application\Helper\EmailHelper;

class Cron extends AbstractActionController {

    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
//        $this->indexAction();
    }

    public function indexAction() {

        $request = $this->getRequest();
        $registeredDevices = [];
        $attendanceDevices = [];
        $missingIpWithData = [];

        if ($request->isGET()) {

            $deviceIpList = $this->getDeviceIp();
            $registeredDevices = $this->getValuesFromArray($deviceIpList);

            $attendanceIpList = $this->getAttendanceIp();
            $attendanceDevices = $this->getValuesFromArray($attendanceIpList);

            $missingIps = array_diff($registeredDevices, $attendanceDevices);

            $missingIpWithData = $this->getDataofMissingIp($missingIps);

            if ($missingIpWithData == NULL) {
                return new JsonModel(['success' => false, 'data' => $missingIpWithData, 'message' => 'No record found']);
            } else {
                $this->prepareEmail($missingIpWithData);
                return new JsonModel(['success' => true, 'data' => $missingIpWithData]);
            }
        }
    }

    public function getDataofMissingIp($missingIps) {
        $cronRepo = new CronRepository($this->adapter);
        for ($x = 0; $x < count($missingIps); $x++) {
            $reqiredData[$x] = $cronRepo->fetchDataOfMissingIp($missingIps[$x])[0];
        }
        return $reqiredData;
    }

    // function to extract only ip values from key-value pair array
    public function getValuesFromArray($ipArray) {
        for ($i = 0; $i < count($ipArray); $i++) {
            $ipAddresses[$i] = $ipArray[$i]['IP_ADDRESS'];
        }
        return $ipAddresses;
    }

//    public function getEmailList() {
//        $cronRepo = new CronRepository($this->adapter);
//        return $cronRepo->fetchEmailList();
//    }

    public function getDataOfDevice($missingIp) {
        $cronRepo = new CronRepository($this->adapter);
        return $cronRepo->fetchEmailOfManager($missingIp);
    }

    public function getDeviceIp() {
        $cronRepo = new CronRepository($this->adapter);
        return $cronRepo->fetchAllDeviceIp();
    }

    public function getAttendanceIp() {
        $cronRepo = new CronRepository($this->adapter);
        return $cronRepo->fetchAllAttendanceIp();
    }

    public function prepareEmail($missingIpInfo) {
        for ($i = 0; $i < count($missingIpInfo); $i++) {
            $to = $missingIpInfo[$i]['EMAIL_OFFICIAL'];
            $absentDate = (string) $missingIpInfo[$i]['ATT_DT'];
            $body = 'Dear ' . $missingIpInfo[$i]['FULL_NAME'] . ', '
                    . 'Attendances from your device with IP: ' . $missingIpInfo[$i]['DEVICE_IP'] . ' for ' . $absentDate . ' has not been recorded.';
            $subject = "Missing Attendance";

            $this->sendEmail($to, $body, $subject);
        }
    }

    public function sendEmail($to, $body, $subject) {
        $msg = new Message();
        $msg->setSubject($subject);
        $msg->setBody($body);
        $msg->setTo($to);
        EmailHelper::sendEmail($msg);
    }

}
