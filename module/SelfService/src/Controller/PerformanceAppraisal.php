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

class PerformanceAppraisal extends AbstractActionController{
    private $repository;
    private $adapter;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $authService = new AuthenticationService();
        $this->employeeId = $authService->getStorage()->read()['employee_id'];
    }
    
    public function indexAction() {
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $result = $appraisalAssignRepo->fetchByEmployeeId($this->employeeId);
        $list = [];
        foreach($result as $row){
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this,['list'=>$list]);
    }
    public function viewAction(){
        $appraisalId = $this->params()->fromRoute('appraisalId');
        $appraisalAssignRepo = new AppraisalAssignRepository($this->adapter);
        $employeeRepo = new EmployeeRepository($this->adapter);
        $headingRepo = new HeadingRepository($this->adapter);
        $questionRepo = new QuestionRepository($this->adapter);
        $stageQuestionRepo = new StageQuestionRepository($this->adapter);
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
            $questionList = $stageQuestionRepo->getByStageIdHeadingId($headingRow['HEADING_ID'],$currentStageId,$appraiseeFlag);
            if(count($questionList)>0){
                array_push($questionTemplate, [
                    'HEADING_ID'=>$headingRow['HEADING_ID'],
                    'HEADING_EDESC'=>$headingRow['HEADING_EDESC'],
                    'QUESTIONS'=>$questionList]);
            }
        }
        return Helper::addFlashMessagesToArray($this,[
            'assignedAppraisalDetail'=> $assignedAppraisalDetail,
            'employeeDetail'=>$employeeDetail,
            'questionTemplate'=>$questionTemplate
            ]);
    }
}