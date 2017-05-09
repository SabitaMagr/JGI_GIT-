<?php
namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use System\Form\PreferenceSetupForm;
use Zend\Form\Annotation\AnnotationBuilder;
use Setup\Model\Company;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Sql\Select;
use System\Model\PreferenceSetup as PreferenceSetupModel;
use System\Repository\PreferenceSetupRepo;

class PreferenceSetup extends AbstractActionController{
    const PREFERENCE_NAME = [
        "OVERTIME_REQUEST"=>"Overtime Request"
    ];
    const PREFERENCE_CONSTRAINT = [
        "OVERTIME_GRACE_TIME" => "Overtime Grace Time"
    ];
    const CONSTRAINT_TYPE = [
        'TEXT'=>'Text',
        'NUMBER'=>"Number",
        'DATE'=>"Date",
        'TIMESTAMP'=>"Timestamp",
        'HOUR'=>"Hour"
    ];
    const PREFERENCE_CONDITION =[
        "BEFORE"=>"Before",
        "AFTER"=>"After",
        "LESS_THAN"=>"Less than",
        "GREATER_THAN"=>"Greater than",
        "EQUAL"=>"Equal"
    ];
    const REQUEST_TYPE = [
        'RQ'=>"Pending",
        'AP'=>'Approved'
    ];
    private $form;
    private $repository;
    private $employeeId;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
        $this->repository = new PreferenceSetupRepo($adapter);
    }
    public function indexAction() {
        $result = $this->repository->fetchAll();
        $list = [];
        foreach ($result as $row){
            $row['PREFERENCE_NAME'] = self::PREFERENCE_NAME[$row['PREFERENCE_NAME']];
            $row['PREFERENCE_CONSTRAINT'] = self::PREFERENCE_CONSTRAINT[$row['PREFERENCE_CONSTRAINT']];
            $row['CONSTRAINT_TYPE'] = self::CONSTRAINT_TYPE[$row['CONSTRAINT_TYPE']];
            $row['PREFERENCE_CONDITION'] = self::PREFERENCE_CONDITION[$row['PREFERENCE_CONDITION']];
            $row['REQUEST_TYPE']=self::REQUEST_TYPE[$row['REQUEST_TYPE']];
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, ['list'=>$list]);
    }
    public function initializeForm(){
        $preferenceForm = new PreferenceSetupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($preferenceForm);
    }
    public function addAction(){
        $this->initializeForm();
        $request = $this->getRequest();
        if($request->isPost()){
            $postData = $request->getPost();
            $this->form->setData($postData);
            if($this->form->isValid()){
                $preferenceSetup = new PreferenceSetupModel();
                $preferenceSetup->exchangeArrayFromForm($this->form->getData());
                $preferenceSetup->preferenceId = ((int) Helper::getMaxId($this->adapter, "HRIS_PREFERENCE_SETUP", "PREFERENCE_ID")) + 1;
                $preferenceSetup->createdDate = Helper::getcurrentExpressionDate();
                $preferenceSetup->createdBy = $this->employeeId;
                $preferenceSetup->status = 'E';

                $this->repository->add($preferenceSetup);

                $this->flashmessenger()->addMessage("Preference Detail Successfully Added!!!");
                return $this->redirect()->toRoute("preferenceSetup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form'=> $this->form,
            'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, Select::ORDER_ASCENDING, null, false, true),
            'preferenceNameList'=>self::PREFERENCE_NAME,
            'preferenceConstraintList'=>self::PREFERENCE_CONSTRAINT,
            'constraintTypeList'=>self::CONSTRAINT_TYPE,
            "preferenceConditionList"=>self::PREFERENCE_CONDITION,
            'requestTypeList'=>self::REQUEST_TYPE
        ]);
    }
    public function editAction(){
        $this->initializeForm();
        $id = $this->params()->fromRoute('id');
        if($id===0){
            $this->redirect()->toRoute('preferenceSetup');
        }
        $request = $this->getRequest();
        $preferenceSetupModel = new PreferenceSetupModel();
        if(!$request->isPost()){
           $detail = $this->repository->fetchById($id)->getArrayCopy();
           $preferenceSetupModel->exchangeArrayFromDB($detail);
           $this->form->bind($preferenceSetupModel);
        }else{
            $postData = $request->getPost();
            $this->form->setData($postData);
            if($this->form->isValid()){
                $preferenceSetup = new PreferenceSetupModel();
                $preferenceSetup->exchangeArrayFromForm($this->form->getData());
                $preferenceSetup->modifiedDate = Helper::getcurrentExpressionDate();
                $preferenceSetup->modifiedBy = $this->employeeId;

                $this->repository->edit($preferenceSetup,$id);

                $this->flashmessenger()->addMessage("Preference Detail Successfully Updated!!!");
                return $this->redirect()->toRoute("preferenceSetup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form'=> $this->form,
            'id'=>$id,
            'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, Select::ORDER_ASCENDING, null, false, true),
            'preferenceNameList'=>self::PREFERENCE_NAME,
            'preferenceConstraintList'=>self::PREFERENCE_CONSTRAINT,
            'constraintTypeList'=>self::CONSTRAINT_TYPE,
            "preferenceConditionList"=>self::PREFERENCE_CONDITION,
            'requestTypeList'=>self::REQUEST_TYPE
        ]);
    }
}
