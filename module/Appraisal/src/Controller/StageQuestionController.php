<?php 
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Appraisal\Repository\StageQuestionRepository;
use Appraisal\Repository\StageRepository;
use Appraisal\Repository\QuestionRepository;
use Appraisal\Repository\HeadingRepository;

class StageQuestionController extends AbstractActionController{
    private $repository;
    private $adapter;
    
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $this->repository = new StageQuestionRepository($adapter);
    }
    
    public function indexAction(){
       $stageRepo = new StageRepository($this->adapter);
       $questionRepo = new QuestionRepository($this->adapter);
       $headingRepo = new HeadingRepository($this->adapter);
       $headingResult = $headingRepo->fetchAll();
       $headingList = [];
       foreach($headingResult as $headingRow){
           array_push($headingList,[
               'HEADING_ID'=>$headingRow['HEADING_ID'],
               'HEADING_EDESC'=>$headingRow['HEADING_EDESC'],
               'HEADING_NDESC'=>$headingRow['HEADING_NDESC'],
               'QUESTIONS'=>$this->generateQuestion($headingRow['HEADING_ID'])
           ]);
       }
       $stageResult = $stageRepo->fetchAll();
       $stageList = [];
       foreach($stageResult as $stageRow){
           array_push($stageList,$stageRow);
       }
       
       return Helper::addFlashMessagesToArray($this, [
           'list'=>"list",
           'stageList'=>$stageList,
           'headingList'=>$headingList
       ]);
    }
    
    public function generateQuestion($headingId){
        $questionRepo = new QuestionRepository($this->adapter);
        $result = $questionRepo->fetchByHeadingId($headingId);
        $questionList = [];
        foreach($result as $row){
            array_push($questionList,[
                'QUESTION_ID'=>$row['QUESTION_ID'],
                'QUESTION_EDESC'=>$row['QUESTION_EDESC']
                ]);
        }
        return $questionList;
    }
}