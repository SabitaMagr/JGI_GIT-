<?php
namespace Appraisal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Appraisal\Form\QuestionForm;
use Appraisal\Model\Question;
use Appraisal\Repository\QuestionRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Appraisal\Model\Heading;
use Setup\Repository\EmployeeRepository;

class QuestionController extends AbstractActionController{
    private $adapter;
    private $repository;
    private $form;
    private $employeeId;
    private $userId;
    
    public function __construct(AdapterInterface $adapter){
        $this->adapter = $adapter;
        $this->repository = new QuestionRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()["employee_id"];
        $this->userId = $auth->getStorage()->read()["user_id"];
    }
    public function initializeForm(){
        $form = new QuestionForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }
    public function indexAction() {
        $result = $this->repository->fetchAll();
        $list = [];
        foreach($result as $row){
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
            'questions'=>$list
        ]);
    }
    public function addAction(){
        $this->initializeForm();
        $request = $this->getRequest();
        $answerTypeList = [
            'TEXT'=>'Text',
            'NUMBER'=>'Number',
            'SELECT'=>'Select',
            'TEXTAREA'=>'Textarea',
            'RADIO'=>'Radio',
            'CHECKBOX'=>'Checkbox',
            'PERCENTAGE'=>'Percentage'
            
        ];
        if($request->isPost()){
            $question = new Question();
            $employeeRepo = new EmployeeRepository($this->adapter);
            $employeeDetail = $employeeRepo->fetchById($this->employeeId);
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
                $question->exchangeArrayFromForm($this->form->getData());
                $question->questionId = ((int)Helper::getMaxId($this->adapter, Question::TABLE_NAME, Question::QUESTION_ID))+1;
                $question->createdBy = $this->employeeId;
                $question->createdDate = Helper::getcurrentExpressionDate();
                $question->approvedDate = Helper::getcurrentExpressionDate();
                $question->companyId = $employeeDetail['COMPANY_ID'];
                $question->branchId = $employeeDetail['BRANCH_ID'];
                $question->status = 'E';
                $this->repository->add($question);
                $this->flashmessenger()->addMessage("Appraisal Question Successfully added!!!");
                return $this->redirect()->toRoute("question");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form'=>$this->form,
            'headings'=> EntityHelper::getTableKVListWithSortOption($this->adapter, Heading::TABLE_NAME, Heading::HEADING_ID, [Heading::HEADING_EDESC],[Heading::STATUS=>'E'], Heading::HEADING_EDESC,"ASC"),
            'answerTypeList'=>$answerTypeList,
            'customRenderer'=>Helper::renderCustomView()
        ]);
    }
    public function editAction(){
        $this->initializeForm();
        $id = $this->params()->fromRoute('id');
        if($id==0){
            $this->redirect()->toRoute('question');
        }
        $request = $this->getRequest();
        $question = new Question();
        $answerTypeList = [
            'TEXT'=>'Text',
            'NUMBER'=>'Number',
            'SELECT'=>'Select',
            'TEXTAREA'=>'Textarea',
            'RADIO'=>'Radio',
            'CHECKBOX'=>'Checkbox',
            'PERCENTAGE'=>'Percentage'
            
        ];
        if(!$request->isPost()){
            $question->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($question);
        }else{
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
                $question->exchangeArrayFromForm($this->form->getData());
                $question->modifiedDate = Helper::getcurrentExpressionDate();
                $question->modifiedBy = $this->employeeId;
                $this->repository->edit($question, $id);
                $this->flashmessenger()->addMessage("Appraisal Question Successfully Updated!!!");
                return $this->redirect()->toRoute("question");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form'=>$this->form,
            'headings'=> EntityHelper::getTableKVListWithSortOption($this->adapter, Heading::TABLE_NAME, Heading::HEADING_ID, [Heading::HEADING_EDESC],[Heading::STATUS=>'E'], Heading::HEADING_EDESC,"ASC"),
            'answerTypeList'=>$answerTypeList,
            'customRenderer'=>Helper::renderCustomView(),
            'id'=>$id
        ]);
    }
    public function deleteAction(){
        $id = $this->params()->fromRoute('id');
        if($id===0){
            $this->redirect()->toRoute('question');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Appraisal Question Successfully Deleted!!!");
        return $this->redirect()->toRoute("question");
    }
}