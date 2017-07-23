<?php
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Appraisal\Repository\AppraisalReportRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\AuthenticationService;
use Application\Helper\Helper;
use Appraisal\Model\Setup;
use Appraisal\Model\Stage;
use Zend\Form\Element\Select;
use Application\Helper\EntityHelper;
use Appraisal\Repository\AppraisalAssignRepository;
use Setup\Repository\EmployeeRepository;
use Appraisal\Repository\HeadingRepository;
use Appraisal\Repository\QuestionRepository;
use Appraisal\Repository\QuestionOptionRepository;
use Appraisal\Repository\StageQuestionRepository;
use Appraisal\Repository\AppraisalAnswerRepository;
use Application\Helper\CustomFormElement;
use Appraisal\Model\AppraisalAnswer;
use Appraisal\Repository\StageRepository;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use Appraisal\Model\Question;
use Application\Helper\AppraisalHelper;
use SelfService\Repository\AppraisalKPIRepository;
use SelfService\Repository\AppraisalCompetenciesRepo;
use Appraisal\Repository\AppraisalStatusRepository;
use Appraisal\Model\AppraisalStatus;

class AppraisalReportController extends AbstractActionController{
    private $repository;
    private $adapter;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $this->repository = new AppraisalReportRepository($this->adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }
    public function indexAction() {
        $appraisalFormElement = new Select();
        $appraisalFormElement->setName("Appraisal");
        $appraisals = EntityHelper::getTableKVListWithSortOption($this->adapter, Setup::TABLE_NAME, Setup::APPRAISAL_ID, [Setup::APPRAISAL_EDESC], [Setup::STATUS => 'E'], Setup::APPRAISAL_EDESC, "ASC",NULL,FALSE,TRUE);
        $appraisals1 = [-1 => "All Type"] + $appraisals;
        $appraisalFormElement->setValueOptions($appraisals1);
        $appraisalFormElement->setAttributes(["id" => "appraisalId", "class" => "form-control"]);
        $appraisalFormElement->setLabel("Appraisal");
        
        $appraisalStageFormElement = new Select();
        $appraisalStageFormElement->setName("Appraisal");
        $appraisalStages = EntityHelper::getTableKVListWithSortOption($this->adapter, Stage::TABLE_NAME, Stage::STAGE_ID, [Stage::STAGE_EDESC], [Stage::STATUS => 'E'], Stage::STAGE_EDESC, "ASC",NULL,FALSE,TRUE);
        $appraisalStages1 = [-1 => "All Stage"] + $appraisalStages;
        $appraisalStageFormElement->setValueOptions($appraisalStages1);
        $appraisalStageFormElement->setAttributes(["id" => "appraisalStageId", "class" => "form-control"]);
        $appraisalStageFormElement->setLabel("Appraisal Stage");

        return Helper::addFlashMessagesToArray($this,[
            'appraisals'=>$appraisalFormElement,
            'appraisalStages'=>$appraisalStageFormElement,
            'userId'=>$this->employeeId,
            'searchValues' => EntityHelper::getSearchData($this->adapter)
                ]);
    }
    public function viewAction(){
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
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($employeeId,$appraisalId);
        $appraisalTypeId = $assignedAppraisalDetail['APPRAISAL_TYPE_ID'];
        $currentStageId = $assignedAppraisalDetail['STAGE_ID'];
        $headingList = $headingRepo->fetchByAppraisalTypeId($appraisalTypeId);
        $questionTemplate = [];
        
        $appraiseeFlag = ["(Q.".Question::APPRAISEE_FLAG."='Y' OR Q.".Question::APPRAISEE_RATING."='Y')"];
        $appraiserFlag = ["(Q.".Question::APPRAISER_FLAG."='Y' OR Q.".Question::APPRAISER_RATING."='Y') AND (Q.".Question::APPRAISEE_FLAG."='N' AND Q.".Question::APPRAISEE_RATING."='N')"];
        $reviewerFlag = ["(Q.".Question::REVIEWER_FLAG."='Y' OR Q.".Question::REVIEWER_RATING."='Y') AND (Q.".Question::APPRAISEE_FLAG."='N' AND Q.".Question::APPRAISEE_RATING."='N') AND (Q.".Question::APPRAISER_FLAG."='N' AND Q.".Question::APPRAISER_RATING."='N')"];
        $hrFlag = ["(Q.".Question::HR_FLAG."='Y' OR Q.".Question::HR_RATING."='Y') AND (Q.".Question::APPRAISEE_FLAG."='N' AND Q.".Question::APPRAISEE_RATING."='N') AND (Q.".Question::APPRAISER_FLAG."='N' AND Q.".Question::APPRAISER_RATING."='N') AND (Q.".Question::REVIEWER_FLAG."='N' AND Q.".Question::REVIEWER_RATING."='N')"];
        
        $appraiserQuestionTemplate = [];
        $appraiseeQuestionTemplate = [];
        $reviewerQuestionTemplate = [];
        $hrQuestionTemplate = [];
        $questionForCurStage = 0;
        $appraiseeAvailableAnswer = false;
        $appraiserAvailableAnswer = false;
        $reviewerAvailableAnswer = false;
        $hrAvailableAnswer = false;
        $hrId = $employeeRepo->fetchByHRFlagList();
        $isHr = (in_array($this->employeeId,$hrId))?true:false;
        foreach($headingList as $headingRow){
            //get question list for appraisee with current stage id
            $questionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter,$headingRow['HEADING_ID'],$currentStageId,$appraiseeFlag,$appraisalId,$employeeId,$employeeId,"=1",[$assignedAppraisalDetail['APPRAISER_ID'],$assignedAppraisalDetail['ALT_APPRAISER_ID']],[$assignedAppraisalDetail['REVIEWER_ID'],$assignedAppraisalDetail['ALT_REVIEWER_ID']],$hrId);
            $appraiserQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter,$headingRow['HEADING_ID'],$currentStageId,$appraiserFlag,$appraisalId,$employeeId,[$assignedAppraisalDetail['APPRAISER_ID'],$assignedAppraisalDetail['ALT_APPRAISER_ID']],null,null,[$assignedAppraisalDetail['REVIEWER_ID'],$assignedAppraisalDetail['ALT_REVIEWER_ID']],$hrId);
            $appraiseeQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter,$headingRow['HEADING_ID'], $currentStageId, $appraiseeFlag, $appraisalId,$employeeId,$employeeId,"!=1");
            $reviewerQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter,$headingRow['HEADING_ID'], $currentStageId, $reviewerFlag, $appraisalId,$employeeId,[$assignedAppraisalDetail['REVIEWER_ID'],$assignedAppraisalDetail['ALT_REVIEWER_ID']],null,null,null,$hrId);
            $hrQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter, $headingRow['HEADING_ID'], $currentStageId, $hrFlag, $appraisalId, $employeeId, $hrId);
//            print_r($hrQuestionList); die();
            if($hrQuestionList['questionForCurStage']){
                $questionForCurStage+=1;
            }
            if($appraiserQuestionList['availableAnswer']){
                $appraiserAvailableAnswer=true;
            }
            if($appraiseeQuestionList['availableAnswer']){
                $appraiseeAvailableAnswer=true;
            }
            if($reviewerQuestionList['availableAnswer']){
                $reviewerAvailableAnswer=true;
            }
            if($hrQuestionList['availableAnswer']){
                $hrAvailableAnswer=true;
            }
            if(count($questionList['questionList'])>0){
                array_push($questionTemplate, [
                    'HEADING_ID'=>$headingRow['HEADING_ID'],
                    'HEADING_EDESC'=>$headingRow['HEADING_EDESC'],
                    'QUESTIONS'=>$questionList['questionList']]);
            }
            if(count($appraiserQuestionList['questionList'])>0){
                array_push($appraiserQuestionTemplate, [
                    'HEADING_ID'=>$headingRow['HEADING_ID'],
                    'HEADING_EDESC'=>$headingRow['HEADING_EDESC'],
                    'QUESTIONS'=>$appraiserQuestionList['questionList']]);
            }
            if(count($appraiseeQuestionList['questionList'])>0){
                array_push($appraiseeQuestionTemplate, [
                    'HEADING_ID'=>$headingRow['HEADING_ID'],
                    'HEADING_EDESC'=>$headingRow['HEADING_EDESC'],
                    'QUESTIONS'=>$appraiseeQuestionList['questionList']]);
            }
            if(count($reviewerQuestionList['questionList'])>0){
                array_push($reviewerQuestionTemplate, [
                    'HEADING_ID'=>$headingRow['HEADING_ID'],
                    'HEADING_EDESC'=>$headingRow['HEADING_EDESC'],
                    'QUESTIONS'=>$reviewerQuestionList['questionList']   ]);
            }
            if(count($hrQuestionList['questionList'])>0){
                array_push($hrQuestionTemplate, [
                    'HEADING_ID'=>$headingRow['HEADING_ID'],
                    'HEADING_EDESC'=>$headingRow['HEADING_EDESC'],
                    'QUESTIONS'=>$hrQuestionList['questionList']   ]);
            }
        }
        $returnData = [
            'assignedAppraisalDetail'=> $assignedAppraisalDetail,
            'employeeDetail'=>$employeeDetail,
            'questionTemplate'=>$questionTemplate,
            'appraiserQuestionTemplate'=>$appraiserQuestionTemplate,
            'appraiseeQuestionTemplate'=>$appraiseeQuestionTemplate,
            'reviewerQuestionTemplate'=>$reviewerQuestionTemplate,
            'questionForCurStage'=>$questionForCurStage,
            'performanceAppraisalObj'=>CustomFormElement::formElement(),
            'customRenderer' => Helper::renderCustomView(),
            'customRendererForCheckbox' => Helper::renderCustomViewForCheckbox(),
            'appraisalId'=>$appraisalId,
            'employeeId'=>$employeeId,
            'appraiseeAvailableAnswer'=>$appraiseeAvailableAnswer,
            'appraiserAvailableAnswer'=>$appraiserAvailableAnswer,
            'reviewerAvailableAnswer'=>$reviewerAvailableAnswer,
            'hrAvailableAnswer'=>$hrAvailableAnswer,
            'hrQuestionTemplate'=>$hrQuestionTemplate,
            'isHr'=>$isHr
        ];
        if($request->isPost()){
            try{
                $appraisalAnswerModel = new AppraisalAnswer();
                $appraisalStatus = new AppraisalStatus();
                $appraisalStatusRepo = new AppraisalStatusRepository($this->adapter);
                $appraisalStatus->exchangeArrayFromDB($appraisalStatusRepo->fetchByEmpAppId($employeeId,$appraisalId)->getArrayCopy());
                $postData = $request->getPost()->getArrayCopy();
                $answer = $postData['answer'];
                $i=0;
                $editMode = false;
                foreach($answer as $key=>$value){
                    if(strpos($key,'hr') !== false ){
                        $appraisalAnswerModel->rating = $value;
                        $appraisalAnswerModel->modifiedDate = Helper::getcurrentExpressionDate();
                        $appraisalAnswerModel->modifiedBy = $this->employeeId;
                        $maxAnswerId = (int)(Helper::getMaxId($this->adapter, AppraisalAnswer::TABLE_NAME, AppraisalAnswer::ANSWER_ID));
                        $answerId = ($postData['answerId'][$i]==0) ? $maxAnswerId : $postData['answerId'][$i];
                        $appraisalAnswerRepo->edit($appraisalAnswerModel,$answerId);
                        unset($appraisalAnswerModel);
                    }else{
                        $appraisalAnswerModel = new AppraisalAnswer();
                        $appraisalAnswerModel->answer =(gettype($value)=='array')? json_encode($value):$value;
                        if($postData['answerId'][$i]==0){
                            $appraisalAnswerModel->answerId = (int)(Helper::getMaxId($this->adapter, AppraisalAnswer::TABLE_NAME, AppraisalAnswer::ANSWER_ID))+1;
                            $appraisalAnswerModel->appraisalId = $appraisalId;
                            $appraisalAnswerModel->employeeId = $employeeId;
                            $appraisalAnswerModel->userId = $this->employeeId;
                            $appraisalAnswerModel->questionId = $key;
                            $appraisalAnswerModel->stageId = $currentStageId;
                            $appraisalAnswerModel->createdDate = Helper::getcurrentExpressionDate();
                            $appraisalAnswerModel->status = 'E';
                            $appraisalAnswerModel->createdBy = $this->employeeId;
                            $appraisalAnswerModel->approvedDate = Helper::getcurrentExpressionDate();
                            $appraisalAnswerModel->companyId = $userDetail['COMPANY_ID'];
                            $appraisalAnswerModel->branchId = $userDetail['BRANCH_ID'];
                            $appraisalAnswerRepo->add($appraisalAnswerModel);
                        }else{
                            $editMode=true;
                            $appraisalAnswerModel->modifiedDate = Helper::getcurrentExpressionDate();
                            $appraisalAnswerModel->modifiedBy = $this->employeeId;
                            $appraisalAnswerRepo->edit($appraisalAnswerModel,$postData['answerId'][$i]);
                        }
                    }
                    $i+=1;
                }
                switch ($tab) {
                    case 1:    
                        $this->redirect()->toRoute("appraisal-report",['action'=>'view','appraisalId'=>$appraisalId,'employeeId'=>$employeeId,'tab'=>2]);
                    break;
                    case 2:   
                        $this->redirect()->toRoute("appraisal-report",['action'=>'view','appraisalId'=>$appraisalId,'employeeId'=>$employeeId,'tab'=>3]);
                    break;
                    case 3: 
                        $this->redirect()->toRoute("appraisal-report",['action'=>'view','appraisalId'=>$appraisalId,'employeeId'=>$employeeId,'tab'=>6]);
                    break;
                    case 6:
                        $nextStageId = AppraisalHelper::getNextStageId($this->adapter,$assignedAppraisalDetail['STAGE_ORDER_NO']+1); // completed stage
                        $appraisalAssignRepo->updateCurrentStageByAppId($nextStageId, $appraisalId, $employeeId);
                        HeadNotification::pushNotification(NotificationEvents::HR_FEEDBACK, $appraisalStatus, $this->adapter, $this,['ID'=>$this->employeeId],['ID'=>$employeeId,'USER_TYPE'=>"APPRAISEE"]);
                        HeadNotification::pushNotification(NotificationEvents::HR_FEEDBACK, $appraisalStatus, $this->adapter, $this,['ID'=>$this->employeeId],['ID'=>$assignedAppraisalDetail['REVIEWER_ID'],'USER_TYPE'=>"REVIEWER"]);
                        HeadNotification::pushNotification(NotificationEvents::HR_FEEDBACK, $appraisalStatus, $this->adapter, $this,['ID'=>$this->employeeId],['ID'=>$assignedAppraisalDetail['APPRAISER_ID'],'USER_TYPE'=>"APPRAISER"]);

                        $this->flashmessenger()->addMessage("Appraisal Successfully Submitted!!");
                        $this->redirect()->toRoute("appraisalReport");
                    break;
                }
            }catch(Exception $e){
                $this->flashmessenger()->addMessage("Appraisal Submit Failed!!");
                $this->flashmessenger()->addMessage($e->getMessage());
            }
        }
        $appraisalKPI = new AppraisalKPIRepository($this->adapter);
        $appraisalCompetencies = new AppraisalCompetenciesRepo($this->adapter);
        $keyAchievementDtlNum = $appraisalKPI->countKeyAchievementDtl($employeeId, $appraisalId)['NUM'];
        $appraiserRatingDtlNum = $appraisalKPI->countAppraiserRatingDtl($employeeId, $appraisalId)['NUM'];
        $appCompetenciesRatingDtlNum = $appraisalCompetencies->countCompetenciesRatingDtl($employeeId,$appraisalId)['NUM'];
        $returnData['tab']=$tab;
        $returnData['keyAchievementDtlNum']=$keyAchievementDtlNum;
        $returnData['appraiserRatingDtlNum']=$appraiserRatingDtlNum;
        $returnData['appCompetenciesRatingDtlNum']=$appCompetenciesRatingDtlNum;
        return Helper::addFlashMessagesToArray($this,$returnData);
    }
}