<?php 
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Appraisal\Repository\StageQuestionRepository;
use Appraisal\Repository\StageRepository;
use Appraisal\Repository\QuestionRepository;
use Appraisal\Repository\HeadingRepository;
use Application\Custom\CustomViewModel;
use Appraisal\Repository\TypeRepository;

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
       $typeRepo = new TypeRepository($this->adapter);
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
       $typeResult = $typeRepo->fetchAll();
       
       return Helper::addFlashMessagesToArray($this, [
           'list'=>"list",
           'stageList'=>$stageList,
           'headingList'=>$headingList,
           'typeResult'=>$typeResult
       ]);
    }
    
    public function generateQuestion($headingId){
        $questionRepo = new QuestionRepository($this->adapter);
        $result = $questionRepo->fetchByHeadingId($headingId);
        $questionList = array();
        foreach($result as $row){
            $questionList[] = array(
                    "text" => $row['QUESTION_EDESC'],
                    "id" => $row['QUESTION_ID'],
                    "icon" => "fa fa-folder icon-state-success"
                );
        }
        return $questionList;
    }
    
    public function headingsAction(){
        $headingRepo = new HeadingRepository($this->adapter);
        $result = $headingRepo->fetchAll();
        $num = count($result);
        if ($num > 0) {
            $temArray = array();
            foreach ($result as $row) {
                $question = $this->generateQuestion($row['HEADING_ID']);
                if ($question) {
                    $temArray[] = array(
                        "text" => $row['HEADING_EDESC'],
                        "id" => $row['HEADING_ID'],
                        "icon" => "fa fa-folder icon-state-success",
                        "children" => $question
                    );
                } else {
                    $temArray[] = array(
                        "text" => $row['HEADING_EDESC'],
                        "id" => $row['HEADING_ID'],
                        "icon" => "fa fa-folder icon-state-success"
                    );
                }
            }
            return new CustomViewModel(["success"=>true,"data"=>$temArray]);
        } else {
            return new CustomViewModel(["success"=>false]);
        }
    }
}