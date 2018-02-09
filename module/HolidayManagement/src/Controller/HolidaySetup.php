<?php

namespace HolidayManagement\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Exception;
use HolidayManagement\Form\HolidayForm;
use HolidayManagement\Model\Holiday;
use HolidayManagement\Repository\HolidayRepository;
use Setup\Model\HolidayDesignation;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class HolidaySetup extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(HolidayRepository::class);
        $this->initializeForm(HolidayForm::class);
    }

    public function indexAction() {
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
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $holiday = new Holiday();
                $holiday->exchangeArrayFromForm($this->form->getData());
                $holiday->createdDt = Helper::getcurrentExpressionDate();
                $holiday->createdBy = $this->employeeId;
                $holiday->status = 'E';
                $holiday->fiscalYear = (int) Helper::getMaxId($this->adapter, "HRIS_FISCAL_YEARS", "FISCAL_YEAR_ID");
                $holiday->holidayId = ((int) Helper::getMaxId($this->adapter, 'HRIS_HOLIDAY_MASTER_SETUP', 'HOLIDAY_ID')) + 1;
                $this->arrayToCSV($holiday, $postData);
                $this->repository->add($holiday);
                $this->repository->holidayAssign($holiday->holidayId);
                $this->flashmessenger()->addMessage("Holiday Successfully added!!!");
                return $this->redirect()->toRoute("holidaysetup");
            }
        }
        return $this->stickFlashMessagesTo([
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    private function arrayToCSV(Holiday &$model, $postData) {
        $arrayToCSV = function(array $list, $isString = false ) {
            $valuesinCSV = "";
            for ($i = 0; $i < sizeof($list); $i++) {
                $value = $isString ? "'{$list[$i]}'" : $list[$i];
                if ($i + 1 == sizeof($list)) {
                    $valuesinCSV .= "{$value}";
                } else {
                    $valuesinCSV .= "{$value},";
                }
            }
            return $valuesinCSV;
        };


        if (isset($postData['company'])) {
            $model->companyId = $arrayToCSV($postData['company']);
        }
        if (isset($postData['branch'])) {
            $model->branchId = $arrayToCSV($postData['branch']);
        }
        if (isset($postData['department'])) {
            $model->departmentId = $arrayToCSV($postData['department']);
        }
        if (isset($postData['designation'])) {
            $model->designationId = $arrayToCSV($postData['designation']);
        }
        if (isset($postData['position'])) {
            $model->positionId = $arrayToCSV($postData['position']);
        }
        $model->serviceTypeId = isset($postData['serviceType']) ? $arrayToCSV($postData['serviceType']) : '';
        $model->employeeType = isset($postData['employeeType']) ? $arrayToCSV($postData['employeeType'], true) : '';
        $model->genderId = isset($postData['gender']) ? $arrayToCSV($postData['gender']) : '';
        $model->employeeId = isset($postData['employee']) ? $arrayToCSV($postData['employee']) : '';
    }

    private function csvToArray($csvList) {
        $array['companyId'] = str_getcsv($csvList['COMPANY_ID']);
        $array['branchId'] = str_getcsv($csvList['BRANCH_ID']);
        $array['departmentId'] = str_getcsv($csvList['DEPARTMENT_ID']);
        $array['designationId'] = str_getcsv($csvList['DESIGNATION_ID']);
        $array['positionId'] = str_getcsv($csvList['POSITION_ID']);
        $array['serviceTypeId'] = str_getcsv($csvList['SERVICE_TYPE_ID']);
        $array['employeeType'] = str_getcsv($csvList['EMPLOYEE_TYPE']);
        $array['genderId'] = str_getcsv($csvList['GENDER_ID']);
        $array['employeeId'] = str_getcsv($csvList['EMPLOYEE_ID']);
        return $array;
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id", 0);

        if ($id === 0) {
            return $this->redirect()->toRoute("holidaysetup");
        }
        $holiday = new Holiday();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $this->form->setData($postData);
            if ($this->form->isValid()) {
                $holiday->exchangeArrayFromForm($this->form->getData());
                $holiday->modifiedDt = Helper::getcurrentExpressionDate();
                $holiday->modifiedBy = $this->employeeId;
                $this->arrayToCSV($holiday, $postData);
                $this->repository->edit($holiday, $id);
                $this->repository->holidayAssign($id);
                $this->flashmessenger()->addMessage("Holiday Successfuly Edited.");
                return $this->redirect()->toRoute("holidaysetup");
            }
        }
        $resultSet = (array) $this->repository->fetchById($id);
        $holiday->exchangeArrayFromDB($resultSet);
        $this->form->bind($holiday);
        $searchSelectedValues = $this->csvToArray($resultSet);
        return $this->stickFlashMessagesTo([
                    'id' => $id,
                    'form' => $this->form,
                    'customRenderer' => Helper::renderCustomView(),
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'searchSelectedValues' => $searchSelectedValues
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("holidaysetup");
        }
        $this->repository->delete($id);
        $this->repository->holidayAssign($id);
        $this->flashmessenger()->addMessage("Holiday Successfully Deleted!!!");
        return $this->redirect()->toRoute('holidaysetup');
    }

    public function listAction() {
        $list = $this->repository->fetchAll();

        return Helper::addFlashMessagesToArray($this, [
                    'holidayList' => $list,
        ]);
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

    public function pullHolidayListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $fromDate = $data['fromDate'];
            $toDate = $data['toDate'];


            $holidayRepository = new HolidayRepository($this->adapter);
            $rawList = $holidayRepository->filterRecords($fromDate, $toDate);
            $list = Helper::extractDbData($rawList);

            return new JsonModel(['success' => true, 'data' => $list, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
