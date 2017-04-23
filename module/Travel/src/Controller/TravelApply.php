<?php
namespace Travel\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Form\TravelRequestForm;
use Setup\Model\HrEmployees;
use SelfService\Repository\TravelRequestRepository;
use SelfService\Model\TravelRequest as TravelRequestModel;
use Setup\Model\Travel;

class TravelApply extends AbstractActionController{
    private $form;
    private $adapter;
    private $travelRequesteRepository;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->travelRequesteRepository = new TravelRequestRepository($adapter);
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new TravelRequestForm();
        $this->form = $builder->createForm($form);
    }
    public function indexAction() {
       return $this->redirect()->toRoute("travelStatus");
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new TravelRequestModel();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->travelId = ((int) Helper::getMaxId($this->adapter, TravelRequestModel::TABLE_NAME, TravelRequestModel::TRAVEL_ID)) + 1;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $model->deductOnSalary = 'Y';
                $this->travelRequesteRepository->add($model);
                $this->flashmessenger()->addMessage("Travel Request Successfully added!!!");
                return $this->redirect()->toRoute("travelStatus");
            }
        }
        $requestType = array(
            'ad'=>'Advance'            
        );
        $transportTypes = array(
            'AP' => 'Aero Plane',
            'OV' => 'Office Vehicles',
            'TI' => 'Taxi',
            'BS' => 'Bus'
        );

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'requestTypes'=>$requestType,
                    'transportTypes' => $transportTypes,
                    'employees'=> EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"],["STATUS"=>'E','RETIRED_FLAG'=>'N'],"FIRST_NAME","ASC"," ",false,true),
        ]);
    }
}