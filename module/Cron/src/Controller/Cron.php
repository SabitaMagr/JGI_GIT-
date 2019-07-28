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
        $responseData = [];
        try {
            $request = $this->getRequest();
            $requestType = $request->getMethod();

            switch ($requestType) {
                case Request::METHOD_GET:
                    $responseData = $this->getAbsentOrLateList();
                    if ($responseData == NULL) {
                        return new JsonModel(['success' => true, 'data' => $responseData, 'message' => 'No data found']);
                    }
                    break;
                default :
                    throw new Exception('The request is unknown');
            }

            $this->getAbsentList($responseData);
            $this->getLateList($responseData);

            return new JsonModel(['success' => true, 'data' => $responseData, 'message' => $requestType]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => $responseData, 'message' => $e->getMessage()]);
        }
    }

    public function nextDayAction() {
        $responseData = [];
        try {
            $request = $this->getRequest();
            $requestType = $request->getMethod();

            switch ($requestType) {
                case Request::METHOD_GET:
                    $responseData = $this->getMissedOrEarlyOutList();
                    if ($responseData == NULL) {
                        return new JsonModel(['success' => true, 'data' => $responseData, 'message' => 'No data found']);
                    }
                    break;
                default :
                    throw new Exception('The request is unknown');
            }

            $this->getEarlyOut($responseData);
            $this->getMissedPunch($responseData);

            return new JsonModel(['success' => true, 'data' => $responseData, 'message' => $requestType]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => $responseData, 'message' => $e->getMessage()]);
        }
    }

    public function getAbsentOrLateList() {
        $cronRepo = new CronRepository($this->adapter);
        return $cronRepo->fetchAbsentOrLate();
    }

    public function getMissedOrEarlyOutList() {
        $cronRepo = new CronRepository($this->adapter);
        return $cronRepo->fetchMissedOrEarlyOut();
    }

    public function getAbsentList($data) {
        $absentList = array();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['OVERALL_STATUS'] == 'AB' && $data[$i]['EMPLOYEE_MAIL'] != null) {
                array_push($absentList, $data[$i]);
            }
        }
        $this->prepareAbsentEmail($absentList);
    }

    public function getLateList($data) {
        $lateList = array();
        for ($i = 0; $i < count($data); $i++) {
            $lateStatus = $data[$i]['LATE_STATUS'];
            if (($lateStatus == 'L' || $lateStatus == 'B' || $lateStatus == 'Y') && $data[$i]['EMPLOYEE_MAIL'] != null) {
                array_push($lateList, $data[$i]);
            }
        }
        $this->prepareLateEmail($lateList);
    }

    public function getMissedPunch($data) {
        $missedPunch = array();
        for ($i = 0; $i < count($data); $i++) {
            $lateStatus = $data[$i]['LATE_STATUS'];
            if (($lateStatus == 'X' || $lateStatus == 'Y') && $data[$i]['EMPLOYEE_MAIL'] != null) {
                array_push($missedPunch, $data[$i]);
            }
        }
        $this->prepareMissedEmail($missedPunch);
    }

    public function getEarlyOut($data) {
        $earlyOut = array();
        for ($i = 0; $i < count($data); $i++) {
            $lateStatus = $data[$i]['LATE_STATUS'];
            if (($lateStatus == 'E' || $lateStatus == 'B') && $data[$i]['EMPLOYEE_MAIL'] != null) {
                array_push($earlyOut, $data[$i]);
            }
        }
        $this->prepareEarlyOutEmail($earlyOut);
    }

    public function prepareEarlyOutEmail($early) {
        for ($i = 0; $i < count($early); $i++) {
            $to = $early[$i]['EMPLOYEE_MAIL'];
            $cc = $early[$i]['MANAGER_MAIL'];
            $body = 'Dear ' . $early[$i]['EMPLOYEE_NAME'] . ', '
                    . 'This is to inform you that your OUT time yesterday was ' . $early[$i]['OUT_TIME'] . '. But your shift ends at ' . $early[$i]['END_TIME'] . '. I hope your supervisor was informed about the early out.';
            $subject = "Early Check Out";

            $this->sendEmail($to, $body, $subject, $cc);
        }
    }

    public function prepareMissedEmail($missed) {
        for ($i = 0; $i < count($missed); $i++) {
            $to = $missed[$i]['EMPLOYEE_MAIL'];
            $cc = $missed[$i]['MANAGER_MAIL'];
            $body = 'Dear ' . $missed[$i]['EMPLOYEE_NAME'] . ', '
                    . 'This is to inform you that your check out was missed for yesterday.';
            $subject = "Missed Punch";

            $this->sendEmail($to, $body, $subject, $cc);
        }
    }

    public function prepareLateEmail($late) {
        for ($i = 0; $i < count($late); $i++) {
            $to = $late[$i]['EMPLOYEE_MAIL'];
            $cc = $late[$i]['MANAGER_MAIL'];
            $body = 'Dear ' . $late[$i]['EMPLOYEE_NAME'] . ', '
                    . 'This is to inform you that your IN time today was ' . $late[$i]['IN_TIME'] . '. But your shift starts at ' . $late[$i]['START_TIME'] . '. I hope your supervisor was informed about the delay.';
            $subject = "Late Check In";

            $this->sendEmail($to, $body, $subject, $cc);
        }
    }

    public function prepareAbsentEmail($absent) {
        for ($i = 0; $i < count($absent); $i++) {
            $to = $absent[$i]['EMPLOYEE_MAIL'];
            $cc = $absent[$i]['MANAGER_MAIL'];
            $date = (string) $absent[$i]['ATTENDANCE_DT'];
            $body = 'This is to Inform you that '
                    . 'Attendance   for date ' . $date . ' of '.$absent[$i]['EMPLOYEE_NAME'].' is not recorded.';
            $subject = "Missing Attendance";

            $this->sendEmail($to, $body, $subject, $cc);
        }
    }

    public function sendEmail($to, $body, $subject, $cc) {
        try {
            $msg = new Message();
            $msg->setSubject($subject);
            $msg->setBody($body);
            $msg->setTo($to);
            if ($cc != null) {
                $msg->setCc($cc);
            }
            return EmailHelper::sendEmail($msg);
        } catch (Exception $ex) {
            return $ex;
        }
    }

}
