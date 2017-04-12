<?php

namespace HolidayManagement\Controller;

use Application\Helper\EntityHelper as EntityHelper2;
use Application\Helper\Helper;
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
        $holidayFormElement->setName("branch");
        $holidays = EntityHelper2::getTableKVList($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME]);
        ksort($holidays);
        $holidayFormElement->setValueOptions($holidays);
        $holidayFormElement->setAttributes(["id" => "holidayId", "class" => "form-control"]);
        $holidayFormElement->setLabel("Holiday");

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = EntityHelper2::getTableKVListWithSortOption($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], ["STATUS" => "E"], Branch::BRANCH_NAME, "ASC");
        $branchFormElement->setValueOptions($branches);
        $branchFormElement->setAttributes(["id" => "branchId", "required" => "required", "class" => "form-control", "multiple" => "multiple"]);
        $branchFormElement->setLabel("Branch");

        $genderFormElement = new Select();
        $genderFormElement->setName("gender");
        $genders = EntityHelper2::getTableKVList($this->adapter, "HRIS_GENDERS", "GENDER_ID", ["GENDER_NAME"]);
        $genders[-1] = "All";
        ksort($genders);

        $holidayList = $this->repository->fetchAll();
        $viewModel = new ViewModel(Helper::addFlashMessagesToArray($this, [
                    'holidayList' => $holidayList,
                    'holidayFormElement' => $holidayFormElement,
                    'branchFormElement' => $branchFormElement,
                    'genderFormElement' => $genderFormElement,
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
                    "genders" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HRIS_GENDERS),
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
                if ($holiday->genderId == -1) {
                    unset($holiday->genderId);
                }
                $holiday->createdDt = Helper::getcurrentExpressionDate();
                $holiday->createdBy = $this->employeeId;
                $holiday->status = 'E';
                $holiday->fiscalYear = (int) Helper::getMaxId($this->adapter, "HRIS_FISCAL_YEARS", "FISCAL_YEAR_ID");

                $branches = $holiday->branchId;
                unset($holiday->branchId);

                $designations = $holiday->designationId;
                unset($holiday->designationId);

                $holiday->holidayId = ((int) Helper::getMaxId($this->adapter, 'HRIS_HOLIDAY_MASTER_SETUP', 'HOLIDAY_ID')) + 1;
                $this->repository->add($holiday);

                $holidayBranch = new HolidayBranch();
                foreach ($branches as $branchId) {
                    $holidayBranch->branchId = $branchId;
                    $holidayBranch->holidayId = $holiday->holidayId;
                    $this->repository->addHolidayBranch($holidayBranch);
                }

                $holidayDesignation = new HolidayDesignation();
                foreach ($designations as $designationId) {
                    $holidayDesignation->designationId = $designationId;
                    $holidayDesignation->holidayId = $holiday->holidayId;
                    $this->repository->addHolidayDesignation($holidayDesignation);
                }
                $this->flashmessenger()->addMessage("Holiday Successfully added!!!");
                return $this->redirect()->toRoute("holidaysetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
                    "genders" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HRIS_GENDERS),
                    'branches' => EntityHelper2::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], ["STATUS" => "E"]),
                    'designations' => EntityHelper2::getTableKVList($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => "E"])
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
        $holidays = EntityHelper2::getTableKVList($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME]);
        ksort($holidays);
        $holidayFormElement->setValueOptions($holidays);
        $holidayFormElement->setAttributes(["id" => "holidayId", "class" => "form-control"]);
        $holidayFormElement->setLabel("Holiday");

        //print_r($holidayFormElement); die();

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches = EntityHelper2::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], ["STATUS" => "E"]);

        ksort($branches);
        $branchFormElement->setValueOptions($branches);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control", "multiple" => "multiple"]);
        $branchFormElement->setLabel("Branch");

        $designationFormElement = new Select();
        $designationFormElement->setName("designation");
        $designations = EntityHelper2::getTableKVList($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], [Designation::STATUS => "E"]);

        ksort($designations);
        $designationFormElement->setValueOptions($designations);
        $designationFormElement->setAttributes(["id" => "designationId", "class" => "form-control", "multiple" => "multiple"]);
        $designationFormElement->setLabel("Designation");

        $genderFormElement = new Select();
        $genderFormElement->setName("gender");
        $genders = EntityHelper2::getTableKVList($this->adapter, "HRIS_GENDERS", "GENDER_ID", ["GENDER_NAME"]);
        $genders[-1] = "All";
        ksort($genders);

        $holidayList = $this->repository->fetchAll();
        $viewModel = new ViewModel(Helper::addFlashMessagesToArray($this, [
                    'holidayList' => $holidays,
                    'selectedHoliday' => $id,
                    'holidayFormElement' => $holidayFormElement,
                    'branchFormElement' => $branchFormElement,
                    'designationFormElement' => $designationFormElement,
                    'genderFormElement' => $genderFormElement,
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
                    "genders" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HRIS_GENDERS),
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

        $branches = EntityHelper2::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME], ["STATUS" => "E"]);
        $branches[-1] = "All";
        ksort($branches);

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branchFormElement->setValueOptions($branches);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control"]);
        $branchFormElement->setLabel("Branch");

        $genders = EntityHelper2::getTableKVList($this->adapter, "HRIS_GENDERS", "GENDER_ID", ["GENDER_NAME"]);
        $genders[-1] = "All";
        ksort($genders);

        $genderFormElement = new Select();
        $genderFormElement->setName("gender");
        $genderFormElement->setValueOptions($genders);
        $genderFormElement->setAttributes(["id" => "genderId", "class" => "form-control"]);
        $genderFormElement->setLabel("Gender");

        return Helper::addFlashMessagesToArray($this, [
                    'holidayList' => $list,
                    'branches' => $branchFormElement,
                    'genders' => $genderFormElement
        ]);
    }

}
