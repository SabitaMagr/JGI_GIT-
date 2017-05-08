<?php

namespace HolidayManagement\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Exception;
use HolidayManagement\Form\HolidayForm;
use HolidayManagement\Model\Holiday;
use HolidayManagement\Model\HolidayBranch;
use HolidayManagement\Repository\HolidayRepository;
use Setup\Helper\EntityHelper;
use Setup\Model\Branch;
use Setup\Model\Designation;
use Setup\Model\HolidayDesignation;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class HolidaySetup extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new HolidayRepository($adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $leaveApplyForm = new HolidayForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction() {
        $this->initializeForm();
        $holidayFormElement = new Select();
        $holidayFormElement->setName("holiday");
        $holidays = ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME], ["STATUS" => "E"], Holiday::HOLIDAY_ENAME, "ASC", NULL, FALSE, TRUE);
        ksort($holidays);
        $holidayFormElement->setValueOptions($holidays);
        $holidayFormElement->setAttributes(["id" => "holidayId", "class" => "form-control"]);
        $holidayFormElement->setLabel("Holiday");


        $holidayList = $this->repository->fetchAll();
        $viewModel = new ViewModel(Helper::addFlashMessagesToArray($this, [
                    'holidayList' => $holidayList,
                    'holidayFormElement' => $holidayFormElement,
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
        ]));
        return $viewModel;
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $holiday = new Holiday();
                $holiday->exchangeArrayFromForm($this->form->getData());

                $holiday->createdDt = Helper::getcurrentExpressionDate();
                $holiday->createdBy = $this->employeeId;
                $holiday->status = 'E';
                $holiday->fiscalYear = (int) Helper::getMaxId($this->adapter, "HRIS_FISCAL_YEARS", "FISCAL_YEAR_ID");



                $holiday->holidayId = ((int) Helper::getMaxId($this->adapter, 'HRIS_HOLIDAY_MASTER_SETUP', 'HOLIDAY_ID')) + 1;
                $this->repository->add($holiday);

                $this->flashmessenger()->addMessage("Holiday Successfully added!!!");
                return $this->redirect()->toRoute("holidaysetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
                        ]
                )
        );
    }

    public function editAction() {
        $this->initializeForm();
        $id = (int) $this->params()->fromRoute("id");

        if ($id === 0) {
            return $this->redirect()->toRoute("holidaysetup");
        }

        $this->initializeForm();
        $holidayFormElement = new Select();
        $holidayFormElement->setName("holiday");
        $holidays = ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME], ["STATUS" => "E"], Holiday::HOLIDAY_ENAME, "ASC", NULL, FALSE, TRUE);
        ksort($holidays);
        $holidayFormElement->setValueOptions($holidays);
        $holidayFormElement->setAttributes(["id" => "holidayId", "class" => "form-control"]);
        $holidayFormElement->setLabel("Holiday");

        //print_r($holidayFormElement); die();



        $holidayList = $this->repository->fetchAll();
        $viewModel = new ViewModel(Helper::addFlashMessagesToArray($this, [
                    'holidayList' => $holidays,
                    'selectedHoliday' => $id,
                    'holidayFormElement' => $holidayFormElement,
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
        ]));
        return $viewModel;
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");

        if ($id === 0) {
            return $this->redirect()->toRoute("holidaysetup");
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Holiday Successfully Deleted!!!");
        return $this->redirect()->toRoute('holidaysetup');
    }

    public function listAction() {
        $list = $this->repository->fetchAll();

        return Helper::addFlashMessagesToArray($this, [
                    'holidayList' => $list,
        ]);
    }

    public function pullHolidayDetailAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception('Request should be post');
            }
            $postedData = $request->getPost();

            $inputData = $postedData->id;
            $holidayRepository = new HolidayRepository($this->adapter);
            $resultSet = $holidayRepository->fetchById($inputData);

            return new CustomViewModel([
                "success" => true,
                "data" => $resultSet,
                "error" => null
            ]);
        } catch (Exception $e) {
            return new CustomViewModel([
                "success" => false,
                "data" => null,
                "error" => $e->getMessage()
            ]);
        }
    }

    public function branchListAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception('Request should be post');
            }
            $id = $request->getPost()->id;
            $data = $this->repository->getBranchListWithHolidayId($id);
            return new CustomViewModel([
                'success' => true,
                'data' => $data,
                'error' => null
            ]);
        } catch (Exception $e) {
            return new CustomViewModel([
                'success' => false,
                'data' => null,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function designationListAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception('Request should be post');
            }
            $id = $request->getPost()->id;
            $designationList = ApplicationEntityHelper::getTableKVList($this->adapter, HolidayDesignation::TABLE_NAME, HolidayDesignation::DESIGNATION_ID, [HolidayDesignation::DESIGNATION_ID], [HolidayDesignation::HOLIDAY_ID => $id]);
            return new CustomViewModel(['success' => true, 'data' => $designationList, 'error' => null]);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => true, 'data' => null, 'error' => $e->getMessage()]);
        }
    }

    public function updateHolidayDetailAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception('Request should be post');
            }
            $postedData = $request->getPost();
            $inputData = $postedData->data;
            $holidayRepository = new HolidayRepository($this->adapter);


            $data = $inputData['dataArray'];

            $holidayModel = new Holiday();

            $holidayModel->holidayCode = (isset($data['holidayCode']) ? $data['holidayCode'] : "" );
            $holidayModel->holidayEname = (isset($data['holidayEname']) ? $data['holidayEname'] : "" );
            $holidayModel->holidayLname = (isset($data['holidayLname']) ? $data['holidayLname'] : "" );
            $holidayModel->startDate = (isset($data['startDate']) ? $data['startDate'] : "" );
            $holidayModel->endDate = (isset($data['endDate']) ? $data['endDate'] : "" );
            $holidayModel->halfday = $data['halfday'];
            $holidayModel->remarks = (isset($data['remarks']) ? $data['remarks'] : "" );
            $holidayModel->modifiedDt = Helper::getcurrentExpressionDate();
            $holidayModel->modifiedBy = $this->employeeId;

            $resultSet = $holidayRepository->edit($holidayModel, $inputData['holidayId']);
            return new CustomViewModel([
                "success" => true,
                "data" => "Holiday Successfully Updated!!",
                'error' => null
            ]);
        } catch (Exception $e) {
            return new CustomViewModel([
                "success" => false,
                "data" => null,
                'error' => $e->getMessage()
            ]);
        }
    }

}
