<?php
namespace Application\Helper;

use Appraisal\Repository\StageQuestionRepository;
use Appraisal\Repository\QuestionOptionRepository;
use Appraisal\Repository\AppraisalAnswerRepository;
use Appraisal\Repository\StageRepository;

class AppraisalHelper{
    public static function getAllQuestionWidOptions($adapter,$headingId,$currentStageId,$flag,$appraisalId=null,$employeeId=null,$userId=null,$orderCondition=null,$appraiserId=null, $reviewerId=null){
        $stageQuestionRepo = new StageQuestionRepository($adapter);
        $questionOptionRepo = new QuestionOptionRepository($adapter);
        $appraisalAnswerRepo = new AppraisalAnswerRepository($adapter);
        $curResult = $stageQuestionRepo->getByStageIdHeadingId($headingId,$currentStageId,$flag,$orderCondition);
        if($curResult==null){
            $result = $appraisalAnswerRepo->getByAppIdEmpIdUserId($headingId,$appraisalId,$employeeId,$userId,$orderCondition,$flag);
        }else{
            $result = $curResult;
        }
        $questionList = [];
        $availableAnswer = false;
        foreach($result as $row){
            $optionList = $questionOptionRepo->fetchByQuestionId($row['QUESTION_ID']);
            $answerDtl  = $appraisalAnswerRepo->fetchByAllDtl($appraisalId, $row['QUESTION_ID'], $employeeId, $userId,$appraiserId,$reviewerId);
            $options = [];
            foreach($optionList as $optionRow){
                $options[$optionRow['OPTION_ID']]=$optionRow['OPTION_EDESC'];
            }
            
            if((!ISSET($answerDtl)|| gettype($answerDtl)=='undefined'|| $answerDtl==null)){
                $answer=[];
            }else{
                $answer[$answerDtl['ANSWER_ID']]=$answerDtl;
                $availableAnswer=true;
            }
            $new_array = array_merge($row, ['QUESTION_OPTIONS'=>$options,"ANSWER"=>$answer]);
            array_push($questionList,$new_array);
            $answer=[];
        }
        return ['questionList'=>$questionList,'questionForCurStage'=>(($curResult==null)?false:true),'availableAnswer'=>$availableAnswer];
    }
    public static function getNextStageId($adapter,$orderNo){
        $stageRepo = new StageRepository($adapter);
        $stageDetail = $stageRepo->getNextStageId($orderNo);
        return $stageDetail['STAGE_ID'];
    }
}