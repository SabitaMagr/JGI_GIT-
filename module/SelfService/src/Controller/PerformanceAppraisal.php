<?php
namespace SelfService\Controller;

use Application\Helper\Helper;
use Appraisal\Repository\AppraisalAssignRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Authentication\AuthenticationService;
use SelfService\Repository\PerformanceAppraisalRepository;
use Setup\Repository\EmployeeRepository;
use Appraisal\Repository\HeadingRepository;
use Appraisal\Repository\QuestionRepository;
use Appraisal\Repository\QuestionOptionRepository;
use Appraisal\Repository\StageQuestionRepository;
use Application\Helper\CustomFormElement;
use Appraisal\Model\AppraisalAnswer;
use Appraisal\Repository\AppraisalAnswerRepository;
use Exception;

class PerformanceAppraisal extends AbstractActionController{
    private $repository;
    private $adapter;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
        $this->repository = new AppraisalAnswerRepository($this->adapter);
    }
    
    public function indexAction() {
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $result = $appraisalAssignRepo->fetchByEmployeeId($this->employeeId);
        $list = [];
        foreach($result as $row){
            $result = $this->repository->fetchByEmpAppraisalId($this->employeeId,$row['APPRAISAL_ID']);
            if($result!=null){
                $row['ALLOW_ADD'] = false;
                $row['ALLOW_EDIT'] = true;
            }else{
                $row['ALLOW_ADD'] = true;
                $row['ALLOW_EDIT'] = false;
            }
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this,['list'=>$list]);
    }
    public function addAction(){
        $request = $this->getRequest();
        $appraisalId = $this->params()->fromRoute('appraisalId');
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $employeeRepo = new EmployeeRepository($this->adapter);
        $headingRepo = new HeadingRepository($this->adapter);
        $employeeDetail = $employeeRepo->getById($this->employeeId);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($this->employeeId,$appraisalId);
        $appraisalTypeId = $assignedAppraisalDetail['APPRAISAL_TYPE_ID'];
        $currentStageId = $assignedAppraisalDetail['CURRENT_STAGE_ID'];
        $headingList = $headingRepo->fetchByAppraisalTypeId($appraisalTypeId);
        $questionTemplate = [];
        
        $appraiseeFlag = ["Q.".\Appraisal\Model\Question::APPRAISEE_FLAG."='Y'"];
        $appraiserFlag = ["Q.".\Appraisal\Model\Question::APPRAISER_FLAG."='Y'"];
        $reviewerFlag = ["Q.".\Appraisal\Model\Question::REVIEWER_FLAG."='Y'"];
        
        foreach($headingList as $headingRow){
            //get question list for appraisee with current stage id
            $questionList =$this->getAllQuestionWidOptions($headingRow['HEADING_ID'],$currentStageId,$appraiseeFlag);
            
            if(count($questionList)>0){
                array_push($questionTemplate, [
                    'HEADING_ID'=>$headingRow['HEADING_ID'],
                    'HEADING_EDESC'=>$headingRow['HEADING_EDESC'],
                    'QUESTIONS'=>$questionList]);
            }
        }
        if($request->isPost()){
            try{
                $appraisalAnswerModel = new AppraisalAnswer();
                $answer = $request->getPost()->getArrayCopy()['answer'];
                foreach($answer as $key=>$value){
                    $appraisalAnswerModel->answerId = (int)(Helper::getMaxId($this->adapter, AppraisalAnswer::TABLE_NAME, AppraisalAnswer::ANSWER_ID))+1;
                    $appraisalAnswerModel->appraisalId = $appraisalId;
                    $appraisalAnswerModel->employeeId = $this->employeeId;
                    $appraisalAnswerModel->userId = $this->employeeId;
                    $appraisalAnswerModel->questionId = $key;
                    $appraisalAnswerModel->stageId = $currentStageId;
                    $appraisalAnswerModel->createdDate = Helper::getcurrentExpressionDate();
                    $appraisalAnswerModel->status = 'E';
                    $appraisalAnswerModel->createdBy = $this->employeeId;
                    $appraisalAnswerModel->approvedDate = Helper::getcurrentExpressionDate();
                    $appraisalAnswerModel->companyId = $employeeDetail['COMPANY_ID'];
                    $appraisalAnswerModel->branchId = $employeeDetail['BRANCH_ID'];
                    if(gettype($value)=='array'){
                        $appraisalAnswerModel->answer = json_encode($value);
                    }else{
                        $appraisalAnswerModel->answer = $value;
                    }
                    $this->repository->add($appraisalAnswerModel);
                }
                $this->flashmessenger()->addMessage("Appraisal Successfully Submitted!!");
                $this->redirect()->toRoute("performanceAppraisal");
            }catch(Exception $e){
                $this->flashmessenger()->addMessage("Appraisal Submit Failed!!");
                $this->flashmessenger()->addMessage($e->getMessage());
            }
        }
        return Helper::addFlashMessagesToArray($this,[
            'assignedAppraisalDetail'=> $assignedAppraisalDetail,
            'employeeDetail'=>$employeeDetail,
            'questionTemplate'=>$questionTemplate,
            'performanceAppraisalObj'=>CustomFormElement::formElement(),
            'customRenderer' => Helper::renderCustomView(),
            'customRendererForCheckbox' => Helper::renderCustomViewForCheckbox()
            ]);
    }
    public function viewAction(){
        $request = $this->getRequest();
        $appraisalId = $this->params()->fromRoute('appraisalId');
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $employeeRepo = new EmployeeRepository($this->adapter);
        $headingRepo = new HeadingRepository($this->adapter);
        $employeeDetail = $employeeRepo->getById($this->employeeId);
        $assignedAppraisalDetail = $appraisalAssignRepo->getEmployeeAppraisalDetail($this->employeeId,$appraisalId);
        $appraisalTypeId = $assignedAppraisalDetail['APPRAISAL_TYPE_ID'];
        $currentStageId = $assignedAppraisalDetail['CURRENT_STAGE_ID'];
        $headingList = $headingRepo->fetchByAppraisalTypeId($appraisalTypeId);
        $questionTemplate = [];
        
        $appraiseeFlag = ["Q.".\Appraisal\Model\Question::APPRAISEE_FLAG."='Y'"];
        $appraiserFlag = ["Q.".\Appraisal\Model\Question::APPRAISER_FLAG."='Y'"];
        $reviewerFlag = ["Q.".\Appraisal\Model\Question::REVIEWER_FLAG."='Y'"];
        
        foreach($headingList as $headingRow){
            //get question list for appraisee with current stage id
            $questionList =$this->getAllQuestionWidOptions($headingRow['HEADING_ID'],$currentStageId,$appraiseeFlag,$appraisalId);
            
            if(count($questionList)>0){
                array_push($questionTemplate, [
                    'HEADING_ID'=>$headingRow['HEADING_ID'],
                    'HEADING_EDESC'=>$headingRow['HEADING_EDESC'],
                    'QUESTIONS'=>$questionList]);
            }
        }
        if($request->isPost()){
            try{
                $appraisalAnswerModel = new AppraisalAnswer();
                $answer = $request->getPost()->getArrayCopy()['answer'];
                foreach($answer as $key=>$value){
                    $appraisalAnswerModel->modifiedDate = Helper::getcurrentExpressionDate();
                    $appraisalAnswerModel->modifiedBy = $this->employeeId;
                    if(gettype($value)=='array'){
                        $appraisalAnswerModel->answer = json_encode($value);
                    }else{
                        $appraisalAnswerModel->answer = $value;
                    }
                    $this->repository->edit($appraisalAnswerModel,$key);
                }
                $this->flashmessenger()->addMessage("Appraisal Successfully Submitted!!");
                $this->redirect()->toRoute("performanceAppraisal");
            }catch(Exception $e){
                $this->flashmessenger()->addMessage("Appraisal Submit Failed!!");
                $this->flashmessenger()->addMessage($e->getMessage());
            }
        }
        return Helper::addFlashMessagesToArray($this,[
            'assignedAppraisalDetail'=> $assignedAppraisalDetail,
            'employeeDetail'=>$employeeDetail,
            'questionTemplate'=>$questionTemplate,
            'performanceAppraisalObj'=>CustomFormElement::formElement(),
            'customRenderer' => Helper::renderCustomView(),
            'customRendererForCheckbox' => Helper::renderCustomViewForCheckbox()
            ]);
    }
    public function getAllQuestionWidOptions($headingId,$currentStageId,$flag,$appraisalId=null){
        $stageQuestionRepo = new StageQuestionRepository($this->adapter);
        $result = $stageQuestionRepo->getByStageIdHeadingId($headingId,$currentStageId,$flag);
        $questionOptionRepo = new QuestionOptionRepository($this->adapter);
        $questionList = [];
        foreach($result as $row){
            $optionList = $questionOptionRepo->fetchByQuestionId($row['QUESTION_ID']);
            $answerDtl  = $this->repository->fetchByAllDtl($appraisalId, $row['QUESTION_ID'], $this->employeeId, $this->employeeId);
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
        return $questionList;
    }
}