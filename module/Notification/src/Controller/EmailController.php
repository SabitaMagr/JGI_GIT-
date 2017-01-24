<?php

namespace Notification\Controller;

use Application\Helper\Helper;
use Notification\Model\AdvanceRequestNotificationModel;
use Notification\Model\AttendanceRequestNotificationModel;
use Notification\Model\EmailTemplate;
use Notification\Model\LeaveRequestNotificationModel;
use Notification\Model\LoanRequestNotificationModel;
use Notification\Model\TrainingReqNotificationModel;
use Notification\Model\TravelReqNotificationModel;
use Notification\Repository\EmailTemplateRepo;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class EmailController extends AbstractActionController {

    private $employeeId;
    private $adapter;
    private $templateRepo;

    const EMAIL_TYPES = [
        1 => "Leave_Request",
        2 => "Leave_Recommend",
        3 => "Leave_Approve",
        4 => "Attendance_Request",
        5 => "Attendance_Approved",
        6 => "Advance_Request",
        7 => "Advance_Recommend",
        8 => "Advance_Approve",
        9 => "Travel_Request",
        10 => "Travel_Recommend",
        11 => "Travel_Approve",
        12 => "Training",
        13 => "Loan_Request",
        14 => "Loan_Recommend",
        15 => "Loan_Approval",
    ];

    private function getVariables() {
        $type1 = new LeaveRequestNotificationModel();
        $type1ObjVars = $type1->getObjectAttrs();

        $type2 = new AttendanceRequestNotificationModel();
        $type2ObjVars = $type2->getObjectAttrs();

        $type3 = new AdvanceRequestNotificationModel();
        $type3ObjVars = $type3->getObjectAttrs();

        $type4 = new TravelReqNotificationModel();
        $type4ObjVars = $type4->getObjectAttrs();

        $type5 = new TrainingReqNotificationModel();
        $type5ObjVars = $type5->getObjectAttrs();

        $type6 = new LoanRequestNotificationModel();
        $type6ObjVars = $type6->getObjectAttrs();
        return [
            1 => $type1ObjVars,
            2 => $type1ObjVars,
            3 => $type1ObjVars,
            4 => $type2ObjVars,
            5 => $type2ObjVars,
            6 => $type3ObjVars,
            7 => $type3ObjVars,
            8 => $type3ObjVars,
            9 => $type4ObjVars,
            10 => $type4ObjVars,
            11 => $type4ObjVars,
            12 => $type5ObjVars,
            13 => $type6ObjVars,
            14 => $type6ObjVars,
            15 => $type6ObjVars,
        ];
    }

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->templateRepo = new EmailTemplateRepo($adapter);

        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $tab = (int) $this->params()->fromRoute('id');
        if ($tab == 0) {
            $tab = array_keys(self::EMAIL_TYPES)[0];
        }
        $templates = $this->templateRepo->fetchAll();
        return Helper::addFlashMessagesToArray($this, [
                    'emailTypes' => self::EMAIL_TYPES,
                    'templates' => $templates,
                    'tab' => $tab,
                    'variables' => $this->getVariables()
        ]);
    }

    public function editAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $postedData = $request->getPost();
            $template = new EmailTemplate();
            $template->subject = $postedData['subject'];
            $template->description = $postedData['description'];

            $cc = [];
            foreach ($postedData['ccEmail'] as $key => $ccEmail) {
                if (isset($ccEmail) && strlen($ccEmail) > 0) {
                    array_push($cc, ['email' => $ccEmail, 'name' => $postedData['ccName'][$key]]);
                }
            }
            $bcc = [];
            foreach ($postedData['bccEmail'] as $key => $bccEmail) {
                if (isset($bccEmail) && strlen($bccEmail) > 0) {
                    array_push($bcc, ['email' => $bccEmail, 'name' => $postedData['bccName'][$key]]);
                }
            }

            $template->cc = json_encode($cc);
            $template->bcc = json_encode($bcc);

            if ($this->templateRepo->fetchById($postedData['id']) == null) {
                $template->id = $postedData['id'];
                $template->createdBy = $this->employeeId;
                $template->createdDt = Helper::getcurrentExpressionDate();
                $this->templateRepo->add($template);
            } else {
                $template->modifiedBy = $this->employeeId;
                $template->modifiedDt = Helper::getcurrentExpressionDate();
                $this->templateRepo->edit($template, $postedData['id']);
            }

            return $this->redirect()->toRoute('email', ['id' => $postedData['id']]);
        }
    }

}
