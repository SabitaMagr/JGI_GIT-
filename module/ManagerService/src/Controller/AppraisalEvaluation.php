<?php
namespace ManagerService\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use ManagerService\Repository\AppraisalEvaluationRepository;
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

class AppraisalEvaluation extends AbstractActionController{
    
    private $employeeId;
    private $repository;
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
        $this->repository = new AppraisalEvaluationRepository($adapter);
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
        
        $appraiseeFlag = ["Q.".\Appraisal\Model\Question::APPRAISEE_FLAG."='Y'"];
        $appraiserFlag = ["Q.".\Appraisal\Model\Question::APPRAISER_FLAG."='Y'"];
        $reviewerFlag = ["Q.".\Appraisal\Model\Question::REVIEWER_FLAG."='Y'"];
        
        $appraiserQuestionTemplate = [];
        $appraiseeQuestionTemplate = [];
        $reviewerQuestionTemplate = [];
        $questionForCurStage = 0;
        foreach($headingList as $headingRow){
            //get question list for appraisee with current stage id
            $questionList =$this->getAllQuestionWidOptions($headingRow['HEADING_ID'],$currentStageId,$appraiseeFlag,$appraisalId,$employeeId,$employeeId,"=1");
            $appraiserQuestionList =$this->getAllQuestionWidOptions($headingRow['HEADING_ID'],$currentStageId,$appraiserFlag,$appraisalId,$employeeId,$this->employeeId);
            $appraiseeQuestionList = $this->getAllQuestionWidOptions($headingRow['HEADING_ID'], $currentStageId, $appraiseeFlag, $appraisalId,$employeeId,$employeeId,"!=1");
            $reviewerQuestionList = $this->getAllQuestionWidOptions($headingRow['HEADING_ID'], $currentStageId, $reviewerFlag, $appraisalId,$employeeId,$assignedAppraisalDetail['REVIEWER_ID']);
            if($appraiserQuestionList['questionForCurStage']){
                $questionForCurStage+=1;
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
        if($request->isPost()){
            try{
                $appraisalAnswerModel = new AppraisalAnswer();
                $postData = $request->getPost()->getArrayCopy();
                $answer = $postData['answer'];
                $i=0;
                foreach($answer as $key=>$value){
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
                        $appraisalAnswerModel->modifiedDate = Helper::getcurrentExpressionDate();
                        $appraisalAnswerModel->modifiedBy = $this->employeeId;
                        $appraisalAnswerRepo->edit($appraisalAnswerModel,$postData['answerId'][$i]);
                    }
                    $i+=1;
                }
                $appraisalAssignRepo->updateCurrentStageByAppId($this->getNextStageId($assignedAppraisalDetail['STAGE_ORDER_NO']+1), $appraisalId, $employeeId);
                $this->flashmessenger()->addMessage("Appraisal Successfully Submitted!!");
                $this->redirect()->toRoute("appraisal-evaluation");
            }catch(Exception $e){
                $this->flashmessenger()->addMessage("Appraisal Submit Failed!!");
                $this->flashmessenger()->addMessage($e->getMessage());
            }
        }
        return Helper::addFlashMessagesToArray($this,[
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
            'employeeId'=>$employeeId
            ]);
    }
    public function getAllQuestionWidOptions($headingId,$currentStageId,$flag,$appraisalId=null,$employeeId=null,$userId=null,$orderCondition=null){
        $stageQuestionRepo = new StageQuestionRepository($this->adapter);
        $questionOptionRepo = new QuestionOptionRepository($this->adapter);
        $appraisalAnswerRepo = new AppraisalAnswerRepository($this->adapter);
        $curResult = $stageQuestionRepo->getByStageIdHeadingId($headingId,$currentStageId,$flag,$orderCondition);
        if($curResult==null){
            $result = $appraisalAnswerRepo->getByAppIdEmpIdUserId($headingId,$appraisalId,$employeeId,$userId,$orderCondition);
        }else{
            $result = $curResult;
        }
        $questionList = [];
        foreach($result as $row){
            $optionList = $questionOptionRepo->fetchByQuestionId($row['QUESTION_ID']);
            $answerDtl  = $appraisalAnswerRepo->fetchByAllDtl($appraisalId, $row['QUESTION_ID'], $employeeId, $userId);
            $options = [];
            foreach($optionList as $optionRow){
                $options[$optionRow['OPTION_ID']]=$optionRow['OPTION_EDESC'];
            }
            
            if((!ISSET($answerDtl)|| gettype($answerDtl)=='undefined'|| $answerDtl==null)){
                $answer=[];
            }else{
                $answer[$answerDtl->getArrayCopy()['ANSWER_ID']]=$answerDtl->getArrayCopy()['ANSWER'];
            }
            $new_array = array_merge($row, ['QUESTION_OPTIONS'=>$options,"ANSWER"=>$answer]);
            array_push($questionList,$new_array);
            $answer=[];
        }
        return ['questionList'=>$questionList,'questionForCurStage'=>(($curResult==null)?false:true)];
    }
    public function getNextStageId($orderNo){
        $stageRepo = new StageRepository($this->adapter);
        $stageDetail = $stageRepo->getNextStageId($orderNo);
        return $stageDetail['STAGE_ID'];
    }
}
