<?php
namespace ManagerService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use ManagerService\Repository\AppraisalReviewRepository;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
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
use Appraisal\Model\Question;
use Application\Helper\AppraisalHelper;

class AppraisalReview extends AbstractActionController{
    
    private $employeeId;
    private $repository;
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
        $this->repository = new AppraisalReviewRepository($adapter);
    }
    public function indexAction() {
        $result = $this->repository->getAllRequest($this->employeeId);
        $list = [];
        foreach($result as $row){
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this,['list'=>$list]);
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
        $employeeDetail = $employeeRepo->getById($employeeId);
        $userDetail = $employeeRepo->getById($this->employeeId);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($employeeId,$appraisalId);
        $appraisalTypeId = $assignedAppraisalDetail['APPRAISAL_TYPE_ID'];
        $currentStageId = $assignedAppraisalDetail['STAGE_ID'];
        $headingList = $headingRepo->fetchByAppraisalTypeId($appraisalTypeId);
        $questionTemplate = [];
        
        $appraiseeFlag = ["(Q.".Question::APPRAISEE_FLAG."='Y' OR Q.".Question::APPRAISEE_RATING."='Y')"];
        $appraiserFlag = ["(Q.".Question::APPRAISER_FLAG."='Y' OR Q.".Question::APPRAISER_RATING."='Y') AND (Q.".Question::APPRAISEE_FLAG."='N' AND Q.".Question::APPRAISEE_RATING."='N')"];
        $reviewerFlag = ["(Q.".Question::REVIEWER_FLAG."='Y' OR Q.".Question::REVIEWER_RATING."='Y') AND (Q.".Question::APPRAISEE_FLAG."='N' AND Q.".Question::APPRAISEE_RATING."='N') AND (Q.".Question::APPRAISER_FLAG."='N' AND Q.".Question::APPRAISER_RATING."='N')"];
        
        $appraiserQuestionTemplate = [];
        $appraiseeQuestionTemplate = [];
        $reviewerQuestionTemplate = [];
        $questionForCurStage = 0;
        $appraiseeAvailableAnswer = false;
        foreach($headingList as $headingRow){
            //get question list for appraisee with current stage id
            $questionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter,$headingRow['HEADING_ID'],$currentStageId,$appraiseeFlag,$appraisalId,$employeeId,$employeeId,"=1",$assignedAppraisalDetail['APPRAISER_ID'],$assignedAppraisalDetail['REVIEWER_ID']);
            $appraiserQuestionList =AppraisalHelper::getAllQuestionWidOptions($this->adapter,$headingRow['HEADING_ID'],$currentStageId,$appraiserFlag,$appraisalId,$employeeId,$assignedAppraisalDetail['APPRAISER_ID'],null,null,$assignedAppraisalDetail['REVIEWER_ID']);
            $appraiseeQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter,$headingRow['HEADING_ID'], $currentStageId, $appraiseeFlag, $appraisalId,$employeeId,$employeeId,"!=1");
            $reviewerQuestionList = AppraisalHelper::getAllQuestionWidOptions($this->adapter,$headingRow['HEADING_ID'], $currentStageId, $reviewerFlag, $appraisalId,$employeeId,$this->employeeId);
            
            if($reviewerQuestionList['questionForCurStage']){
                $questionForCurStage+=1;
            }
            if($appraiseeQuestionList['availableAnswer']){
                $appraiseeAvailableAnswer=true;
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
            'appraiseeAvailableAnswer'=>$appraiseeAvailableAnswer
        ];
        if($request->isPost()){
            try{
                $appraisalAnswerModel = new AppraisalAnswer();
                $postData = $request->getPost()->getArrayCopy();
                $answer = $postData['answer'];
                $i=0;
                $editMode = false;
//                print "<pre>";
//                print_r($postData); die();
                foreach($answer as $key=>$value){
                    if(strpos($key,'rr') !== false ){
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
                        $this->redirect()->toRoute("appraisal-review",['action'=>'view','appraisalId'=>$appraisalId,'employeeId'=>$employeeId,'tab'=>2]);
                    break;
                    case 2:   
                        $this->redirect()->toRoute("appraisal-review",['action'=>'view','appraisalId'=>$appraisalId,'employeeId'=>$employeeId,'tab'=>3]);
                    break;
                    case 3: 
                        if(!$editMode){
                            $appraisalAssignRepo->updateCurrentStageByAppId(AppraisalHelper::getNextStageId($this->adapter,$assignedAppraisalDetail['STAGE_ORDER_NO']+1), $appraisalId, $employeeId);
                        }
                        $this->flashmessenger()->addMessage("Appraisal Successfully Submitted!!");
                        $this->redirect()->toRoute("appraisal-review");
                    break;
                }
            }catch(Exception $e){
                $this->flashmessenger()->addMessage("Appraisal Submit Failed!!");
                $this->flashmessenger()->addMessage($e->getMessage());
            }
        }
        $returnData['tab']=$tab;
        return Helper::addFlashMessagesToArray($this,$returnData);
    }
}
