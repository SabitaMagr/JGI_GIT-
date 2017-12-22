<?php

namespace ManagerService\Controller;

use Application\Helper\AppraisalHelper;
use Application\Helper\CustomFormElement;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Appraisal\Model\AppraisalAnswer;
use Appraisal\Model\AppraisalStatus;
use Appraisal\Model\Question;
use Appraisal\Model\Setup;
use Appraisal\Model\Stage;
use Appraisal\Repository\AppraisalAnswerRepository;
use Appraisal\Repository\AppraisalAssignRepository;
use Appraisal\Repository\AppraisalStatusRepository;
use Appraisal\Repository\HeadingRepository;
use ManagerService\Repository\AppraisalReviewRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Repository\AppraisalCompetenciesRepo;
use SelfService\Repository\AppraisalKPIRepository;
use Setup\Repository\EmployeeRepository;
use TheSeer\Tokenizer\Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;

class AppraisalFinalReview extends AbstractActionController {

    private $employeeId;
    private $repository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
        $this->repository = new AppraisalReviewRepository($adapter);
    }

    public function indexAction() {
        $appraisalFormElement = new Select();
        $appraisalFormElement->setName("Appraisal");
        $appraisals = EntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::APPRAISAL_ID, [Setup::APPRAISAL_EDESC], [Setup::STATUS => 'E'], Setup::APPRAISAL_EDESC, "ASC", NULL, FALSE, TRUE);
        $appraisals1 = [-1 => "All Type"] + $appraisals;
        $appraisalFormElement->setValueOptions($appraisals1);
        $appraisalFormElement->setAttributes(["id" => "appraisalId", "class" => "form-control"]);
        $appraisalFormElement->setLabel("Appraisal");

        $appraisalStageFormElement = new Select();
        $appraisalStageFormElement->setName("Appraisal");
        $appraisalStages = EntityHelper::getTableKVListWithSortOption($this->adapter, Stage::TABLE_NAME, Stage::STAGE_ID, [Stage::STAGE_EDESC], [Stage::STATUS => 'E'], Stage::STAGE_EDESC, "ASC", NULL, FALSE, TRUE);
        $appraisalStages1 = [-1 => "All Stage"] + $appraisalStages;
        $appraisalStageFormElement->setValueOptions($appraisalStages1);
        $appraisalStageFormElement->setAttributes(["id" => "appraisalStageId", "class" => "form-control"]);
        $appraisalStageFormElement->setLabel("Appraisal Stage");

        return Helper::addFlashMessagesToArray($this, [
                    'appraisals' => $appraisalFormElement,
                    'appraisalStages' => $appraisalStageFormElement,
                    'userId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function monthlyAction() {
        $appraisalFormElement = new Select();
        $appraisalFormElement->setName("Appraisal");
        $appraisals = EntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::APPRAISAL_ID, [Setup::APPRAISAL_EDESC], [Setup::STATUS => 'E'], Setup::APPRAISAL_EDESC, "ASC", NULL, FALSE, TRUE);
        $appraisals1 = [-1 => "All Type"] + $appraisals;
        $appraisalFormElement->setValueOptions($appraisals1);
        $appraisalFormElement->setAttributes(["id" => "appraisalId", "class" => "form-control"]);
        $appraisalFormElement->setLabel("Appraisal");

        $appraisalStageFormElement = new Select();
        $appraisalStageFormElement->setName("Appraisal");
        $appraisalStages = EntityHelper::getTableKVListWithSortOption($this->adapter, Stage::TABLE_NAME, Stage::STAGE_ID, [Stage::STAGE_EDESC], [Stage::STATUS => 'E'], Stage::STAGE_EDESC, "ASC", NULL, FALSE, TRUE);
        $appraisalStages1 = [-1 => "All Stage"] + $appraisalStages;
        $appraisalStageFormElement->setValueOptions($appraisalStages1);
        $appraisalStageFormElement->setAttributes(["id" => "appraisalStageId", "class" => "form-control"]);
        $appraisalStageFormElement->setLabel("Appraisal Stage");

        return Helper::addFlashMessagesToArray($this, [
                    'appraisals' => $appraisalFormElement,
                    'appraisalStages' => $appraisalStageFormElement,
                    'userId' => $this->employeeId,
                    'searchValues' => EntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function viewAction() {
        $request = $this->getRequest();
        $appraisalId = $this->params()->fromRoute('appraisalId');
        $employeeId = $this->params()->fromRoute('employeeId');
        $tab = $this->params()->fromRoute('tab');
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $appraisalAnswerRepo = new AppraisalAnswerRepository($this->adapter);
        $employeeRepo = new EmployeeRepository($this->adapter);
        $headingRepo = new HeadingRepository($this->adapter);
        $employeeDetail = $employeeRepo->fetchForProfileById($employeeId);
        $userDetail = $employeeRepo->getById($this->employeeId);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($employeeId, $appraisalId);
        $appraisalTypeId = $assignedAppraisalDetail['APPRAISAL_TYPE_ID'];
        $currentStageId = $assignedAppraisalDetail['STAGE_ID'];
        $headingList = $headingRepo->fetchByAppraisalTypeId($appraisalTypeId);
        $questionTemplate = [];

        $appraiseeFlag = ["(Q." . Question::APPRAISEE_FLAG . "='Y' OR Q." . Question::APPRAISEE_RATING . "='Y')"];
        $appraiserFlag = ["(Q." . Question::APPRAISER_FLAG . "='Y' OR Q." . Question::APPRAISER_RATING . "='Y') AND (Q." . Question::APPRAISEE_FLAG . "='N' AND Q." . Question::APPRAISEE_RATING . "='N')"];
        $reviewerFlag = ["(Q." . Question::REVIEWER_FLAG . "='Y' OR Q." . Question::REVIEWER_RATING . "='Y') AND (Q." . Question::APPRAISEE_FLAG . "='N' AND Q." . Question::APPRAISEE_RATING . "='N') AND (Q." . Question::APPRAISER_FLAG . "='N' AND Q." . Question::APPRAISER_RATING . "='N')"];

        $appraiserQuestionTemplate = [];
        $appraiseeQuestionTemplate = [];
        $reviewerQuestionTemplate = [];
        $questionForCurStage = 0;
        $appraiseeAvailableAnswer = false;
        $appraiserAvailableAnswer = false;
        $reviewerAvailableAnswer = false;
        foreach ($headingList as $headingRow) {
            //get question list for appraisee with current stage id
            $questionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $appraiseeFlag, $appraisalId, $employeeId, $employeeId, "=1", [$assignedAppraisalDetail['APPRAISER_ID'], $assignedAppraisalDetail['ALT_APPRAISER_ID']], [$assignedAppraisalDetail['REVIEWER_ID'], $assignedAppraisalDetail['ALT_REVIEWER_ID']]);
            $appraiserQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $appraiserFlag, $appraisalId, $employeeId, [$assignedAppraisalDetail['APPRAISER_ID'], $assignedAppraisalDetail['ALT_APPRAISER_ID']], null, null, [$assignedAppraisalDetail['REVIEWER_ID'], $assignedAppraisalDetail['ALT_REVIEWER_ID']]);
            $appraiseeQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $appraiseeFlag, $appraisalId, $employeeId, $employeeId, "!=1");
            $reviewerQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $reviewerFlag, $appraisalId, $employeeId, [$assignedAppraisalDetail['REVIEWER_ID'], $assignedAppraisalDetail['ALT_REVIEWER_ID']]);

            if ($reviewerQuestionList['questionForCurStage']) {
                $questionForCurStage += 1;
            }
            if ($appraiserQuestionList['availableAnswer']) {
                $appraiserAvailableAnswer = true;
            }
            if ($appraiseeQuestionList['availableAnswer']) {
                $appraiseeAvailableAnswer = true;
            }
            if ($reviewerQuestionList['availableAnswer']) {
                $reviewerAvailableAnswer = true;
            }
            if (count($questionList['questionList']) > 0) {
                array_push($questionTemplate, [
                    'HEADING_ID' => $headingRow['HEADING_ID'],
                    'HEADING_EDESC' => $headingRow['HEADING_EDESC'],
                    'QUESTIONS' => $questionList['questionList']]);
            }
            if (count($appraiserQuestionList['questionList']) > 0) {
                array_push($appraiserQuestionTemplate, [
                    'HEADING_ID' => $headingRow['HEADING_ID'],
                    'HEADING_EDESC' => $headingRow['HEADING_EDESC'],
                    'QUESTIONS' => $appraiserQuestionList['questionList']]);
            }
            if (count($appraiseeQuestionList['questionList']) > 0) {
                array_push($appraiseeQuestionTemplate, [
                    'HEADING_ID' => $headingRow['HEADING_ID'],
                    'HEADING_EDESC' => $headingRow['HEADING_EDESC'],
                    'QUESTIONS' => $appraiseeQuestionList['questionList']]);
            }
            if (count($reviewerQuestionList['questionList']) > 0) {
                array_push($reviewerQuestionTemplate, [
                    'HEADING_ID' => $headingRow['HEADING_ID'],
                    'HEADING_EDESC' => $headingRow['HEADING_EDESC'],
                    'QUESTIONS' => $reviewerQuestionList['questionList']]);
            }
        }
        $returnData = [
            'assignedAppraisalDetail' => $assignedAppraisalDetail,
            'employeeDetail' => $employeeDetail,
            'questionTemplate' => $questionTemplate,
            'appraiserQuestionTemplate' => $appraiserQuestionTemplate,
            'appraiseeQuestionTemplate' => $appraiseeQuestionTemplate,
            'reviewerQuestionTemplate' => $reviewerQuestionTemplate,
            'questionForCurStage' => $questionForCurStage,
            'performanceAppraisalObj' => CustomFormElement::formElement(),
            'customRenderer' => Helper::renderCustomView(),
            'customRendererForCheckbox' => Helper::renderCustomViewForCheckbox(),
            'appraisalId' => $appraisalId,
            'employeeId' => $employeeId,
            'tab' => $tab,
            'appraiseeAvailableAnswer' => $appraiseeAvailableAnswer,
            'appraiserAvailableAnswer' => $appraiserAvailableAnswer,
            'loggedInEmployeeId' => $this->employeeId,
            'reviewerAvailableAnswer' => $reviewerAvailableAnswer
        ];
        if ($request->isPost()) {
            try {
                $appraisalAnswerModel = new AppraisalAnswer();
                $appraisalStatus = new AppraisalStatus();
                $appraisalStatusRepo = new AppraisalStatusRepository($this->adapter);
                $appraisalStatus->exchangeArrayFromDB($appraisalStatusRepo->fetchByEmpAppId($employeeId, $appraisalId)->getArrayCopy());
                $postData = $request->getPost()->getArrayCopy();
                $superReviewerAgree = (!isset($postData['superReviewerAgree'])) ? null : $postData['superReviewerAgree'];
                $appraisalStatusRepo->updateColumnByEmpAppId([AppraisalStatus::SUPER_REVIEWER_FEEDBACK => $postData['superReviewerComment'], AppraisalStatus::SUPER_REVIEWER_AGREE => $superReviewerAgree], $appraisalId, $employeeId);
                $nextStageId = ($superReviewerAgree == 'N') ? 5 : 6; //appraiser evaluation:appraisee stage
                $appraisalAssignRepo->updateCurrentStageByAppId($nextStageId, $appraisalId, $employeeId);
                if ($superReviewerAgree === 'Y') {
                    HeadNotification::pushNotification(NotificationEvents::APPRAISAL_FINAL_REVIEW, $appraisalStatus, $this->adapter, $this, ['ID' => $this->employeeId], ['ID' => $employeeId, 'USER_TYPE' => "APPRAISEE"]);
                }
                HeadNotification::pushNotification(NotificationEvents::APPRAISAL_FINAL_REVIEW, $appraisalStatus, $this->adapter, $this, ['ID' => $this->employeeId], ['ID' => $assignedAppraisalDetail['REVIEWER_ID'], 'USER_TYPE' => "REVIEWER"]);
                HeadNotification::pushNotification(NotificationEvents::APPRAISAL_FINAL_REVIEW, $appraisalStatus, $this->adapter, $this, ['ID' => $this->employeeId], ['ID' => $assignedAppraisalDetail['APPRAISER_ID'], 'USER_TYPE' => "APPRAISER"]);
                $this->flashmessenger()->addMessage("Appraisal Successfully Submitted!!");
                $this->redirect()->toRoute("appraisal-final-review");
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage("Appraisal Submit Failed!!");
                $this->flashmessenger()->addMessage($e->getMessage());
            }
        }
        $defaultRatingDtl = AppraisalHelper::checkDefaultRatingForEmp($this->adapter, $employeeId, $appraisalTypeId);
        $appraisalKPI = new AppraisalKPIRepository($this->adapter);
        $appraisalCompetencies = new AppraisalCompetenciesRepo($this->adapter);
        $keyAchievementDtlNum = $appraisalKPI->countKeyAchievementDtl($employeeId, $appraisalId)['NUM'];
        $appraiserRatingDtlNum = $appraisalKPI->countAppraiserRatingDtl($employeeId, $appraisalId)['NUM'];
        $appCompetenciesRatingDtlNum = $appraisalCompetencies->countCompetenciesRatingDtl($employeeId, $appraisalId)['NUM'];
        $returnData['keyAchievementDtlNum'] = $keyAchievementDtlNum;
        $returnData['appraiserRatingDtlNum'] = $appraiserRatingDtlNum;
        $returnData['appCompetenciesRatingDtlNum'] = $appCompetenciesRatingDtlNum;
        $returnData['defaultRatingDtl'] = $defaultRatingDtl;
        return Helper::addFlashMessagesToArray($this, $returnData);
    }

}
