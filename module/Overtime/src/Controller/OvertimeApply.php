<?php
namespace Overtime\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Form\OvertimeRequestForm;
use Setup\Model\HrEmployees;
use SelfService\Repository\OvertimeRepository;
use SelfService\Repository\OvertimeDetailRepository;
use SelfService\Model\Overtime;
use SelfService\Model\OvertimeDetail;

class OvertimeApply extends AbstractActionController{
    private $form;
    private $adapter;
    private $overtimeRepository;
    private $overtimeDetailRepository;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->overtimeRepository = new OvertimeRepository($adapter);
        $this->overtimeDetailRepository = new OvertimeDetailRepository($adapter);
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new OvertimeRequestForm();
        $this->form = $builder->createForm($form);
    }
    public function indexAction() {
       return $this->redirect()->toRoute("overtimeStatus");
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new Overtime();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
             if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->overtimeId = ((int) Helper::getMaxId($this->adapter, Overtime::TABLE_NAME, Overtime::OVERTIME_ID)) + 1;
                $model->employeeId = $this->employeeId;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $this->overtimeRepository->add($model);
                
                $postDataArray = $postData->getArrayCopy();
                $overtimeDetailNum = $postDataArray['overtimeDetailNum'];
                $overtimeDetailModel = new OvertimeDetail();
                for($i=0; $i<=$overtimeDetailNum; $i++){
                    $startTime = $postDataArray['startTime'][$i];
                    $endTime = $postDataArray['endTime'][$i];
                    $totalHour = $postDataArray['totalHour'][$i];
                    $overtimeDetailModel->overtimeId = $model->overtimeId;
                    $overtimeDetailModel->detailId = ((int) Helper::getMaxId($this->adapter, OvertimeDetail::TABLE_NAME, OvertimeDetail::DETAIL_ID)) + 1;
                    $overtimeDetailModel->startTime = Helper::getExpressionTime($startTime);
                    $overtimeDetailModel->endTime = Helper::getExpressionTime($endTime);
                    $overtimeDetailModel->totalHour = $totalHour;
                    $overtimeDetailModel->status = 'E';
                    $overtimeDetailModel->createdBy = $this->employeeId;
                    $overtimeDetailModel->createdDate = Helper::getcurrentExpressionDate();
                    $this->overtimeDetailRepository->add($overtimeDetailModel);
                }
                $this->flashmessenger()->addMessage("Overtime Request Successfully added!!!");
                return $this->redirect()->toRoute("overtimeStatus");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employees'=> EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"],["STATUS"=>'E','RETIRED_FLAG'=>'N'],"FIRST_NAME","ASC"," ",false,true),
        ]);
    }
}