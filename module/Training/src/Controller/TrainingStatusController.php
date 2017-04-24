<?php
namespace Training\Controller;

use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Training\Repository\TrainingStatusRepository;
use ManagerService\Repository\TrainingApproveRepository;
use SelfService\Repository\TrainingRequestRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Form\TrainingRequestForm;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Model\TrainingRequest;
use Setup\Repository\TrainingRepository;
use Setup\Model\Branch;
use Setup\Model\Department;
use Setup\Model\Designation;
use Setup\Model\Position;
use Setup\Model\ServiceType;
use Zend\Form\Element\Select;
use Setup\Model\ServiceEventType;
use Zend\Authentication\AuthenticationService;
use Setup\Repository\RecommendApproveRepository;
use Setup\Repository\EmployeeRepository;

class TrainingStatusController extends AbstractActionController
{
    private $adapter;
    private $trainingApproveRepository;
    private $trainingStatusRepository;
    private $form;
    private $employeeId;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->trainingApproveRepository = new TrainingApproveRepository($adapter);
        $this->trainingStatusRepository = new TrainingStatusRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId =  $auth->getStorage()->read()['employee_id'];       
    }
    
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new TrainingRequestForm();
        $this->form = $builder->createForm($form);
    }

    
    public function indexAction() {
        $status = [
            '-1' => 'All Status',
            'RQ' => 'Pending',
            'RC' => 'Recommended',
            'AP' => 'Approved',
            'R' => 'Rejected',
            'C'=>'Cancelled'
        ];
        $statusFormElement = new Select();
        $statusFormElement->setName("status");
        $statusFormElement->setValueOptions($status);
        $statusFormElement->setAttributes(["id" => "requestStatusId", "class" => "form-control"]);
        $statusFormElement->setLabel("Status");

        return Helper::addFlashMessagesToArray($this, [
                    'status' => $statusFormElement,
                    'searchValues'=> EntityHelper::getSearchData($this->adapter)
        ]);
    }
    public function viewAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute('id');

        if ($id === 0) {
            return $this->redirect()->toRoute("trainingStatus");
        }
        $trainingRequestModel = new TrainingRequest();
        $request = $this->getRequest();

        $detail = $this->trainingApproveRepository->fetchById($id);
        $status = $detail['STATUS'];
        $employeeId = $detail['EMPLOYEE_ID'];
        $approvedDT = $detail['APPROVED_DATE'];

        $requestedEmployeeID = $detail['EMPLOYEE_ID'];
        $recommendApproveRepository = new RecommendApproveRepository($this->adapter);
        $empRecommendApprove = $recommendApproveRepository->fetchById($requestedEmployeeID);
        $recommApprove = 0;
        if($empRecommendApprove['RECOMMEND_BY']==$empRecommendApprove['APPROVED_BY']){
            $recommApprove=1;
        }
        
        $fullName = function($id) {
            $empRepository = new EmployeeRepository($this->adapter);
            $empDtl = $empRepository->fetchById($id);
            $empMiddleName = ($empDtl['MIDDLE_NAME'] != null) ? " " . $empDtl['MIDDLE_NAME'] . " " : " ";
            return $empDtl['FIRST_NAME'] . $empMiddleName . $empDtl['LAST_NAME'];
        };
        
        $employeeName = $fullName($detail['EMPLOYEE_ID']);        
        $authRecommender = ($status=='RQ' || $status=='C')? $detail['RECOMMENDER'] : $detail['RECOMMENDED_BY'];
        $authApprover = ($status=='RC' || $status=='C' || $status=='RQ' || ($status=='R' && $approvedDT==null))? $detail['APPROVER'] : $detail['APPROVED_BY'];

        if($detail['TRAINING_ID']!=0){
            $detail['START_DATE']=$detail['T_START_DATE'];
            $detail['END_DATE'] = $detail['T_END_DATE'];
            $detail['DURATION'] = $detail['T_DURATION'];
            $detail['TRAINING_TYPE'] = $detail['T_TRAINING_TYPE'];
        }
        if (!$request->isPost()) {
            $trainingRequestModel->exchangeArrayFromDB($detail);
            $this->form->bind($trainingRequestModel);
        } else {
            $getData = $request->getPost();
            $reason = $getData->approvedRemarks;
            $action = $getData->submit;

            $trainingRequestModel->approvedDate = Helper::getcurrentExpressionDate();
            if ($action == "Reject") {
                $trainingRequestModel->status = "R";
                $this->flashmessenger()->addMessage("Training Request Rejected!!!");
            } else if ($action == "Approve") {
                $trainingRequestModel->status = "AP";
                $this->flashmessenger()->addMessage("Training Request Approved");
            }
            $trainingRequestModel->approvedBy = $this->employeeId;
            $trainingRequestModel->approvedRemarks = $reason;
            $this->trainingApproveRepository->edit($trainingRequestModel, $id);

            return $this->redirect()->toRoute("trainingStatus");
        }
        $trainingTypes = array(
           'CP'=>'Company Personal',
           'CC'=>'Company Contribution'
        );
        $trainings = $this->getTrainingList($requestedEmployeeID);
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'employeeId' => $employeeId,
                    'employeeName' => $employeeName,
                    'requestedDt' => $detail['REQUESTED_DATE'],
                    'recommender' => $fullName($authRecommender),
                    'approver' => $fullName($authApprover),
                    'approvedDT'=>$detail['APPROVED_DATE'],
                    'status' => $status,
                    'customRenderer' => Helper::renderCustomView(),
                    'recommApprove'=>$recommApprove,
                    'trainingIdSelected'=>$detail['TRAINING_ID'],
                    'trainings' => $trainings["trainingKVList"],
                    'trainingList'=>$trainings['trainingList'],
                    'trainingTypes'=>$trainingTypes,
        ]);
    }
    public function getTrainingList($employeeId) {
        $trainingRepo = new TrainingRepository($this->adapter);
        $trainingResult = $trainingRepo->selectAll($employeeId);
        $trainingList = [];
        $allTrainings = [];
        foreach ($trainingResult as $trainingRow) {
            $trainingList[$trainingRow['TRAINING_ID']] = $trainingRow['TRAINING_NAME'] . " (" . $trainingRow['START_DATE'] . " to " . $trainingRow['END_DATE'] . ")";
            $allTrainings[$trainingRow['TRAINING_ID']] = $trainingRow;
        }
        return ['trainingKVList' => $trainingList,'trainingList'=>$allTrainings];
    }   
}