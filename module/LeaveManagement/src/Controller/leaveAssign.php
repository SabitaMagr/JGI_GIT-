<?php

namespace LeaveManagement\Controller;

use Application\Helper\EntityHelper as AppEntityHelper;
use Application\Helper\Helper;
use LeaveManagement\Form\ExcelImportForm;
use LeaveManagement\Form\LeaveAssignForm;
use LeaveManagement\Model\LeaveMaster;
use LeaveManagement\Repository\LeaveAssignRepository;
use LeaveManagement\Repository\LeaveBalanceRepository;
use LeaveManagement\Repository\LeaveMasterRepository;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use function Zend\Filter\File\move_uploaded_file;

class leaveAssign extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $excelImportForm;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new LeaveAssignRepository($adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $leaveAssignForm = new LeaveAssignForm();
        $excelImportForm = new ExcelImportForm();
        $this->builder = new AnnotationBuilder();
        $this->form = $this->builder->createForm($leaveAssignForm);
        $this->excelImportForm = $this->builder->createForm($excelImportForm);
    }

    public function assignAction() {
        $this->initializeForm();

        $leaveFormElement = new Select();
        $leaveFormElement->setName("leave");
        $leaveFormElement->setLabel("Leave Type");
        $leaveFormElement->setValueOptions(AppEntityHelper::getTableKVListWithSortOption($this->adapter, LeaveMaster::TABLE_NAME, LeaveMaster::LEAVE_ID, [LeaveMaster::LEAVE_ENAME], [LeaveMaster::STATUS . " ='E'"], LeaveMaster::LEAVE_ID, "ASC", NULL, FALSE, TRUE));
        $leaveFormElement->setAttributes(["id" => "leaveId", "class" => "form-control", "data-init-plugin" => "select2"]);

        $genderFormElement = new Select();
        $genderFormElement->setName("gender");
        $genders = AppEntityHelper::getTableKVList($this->adapter, "HRIS_GENDERS", "GENDER_ID", ["GENDER_NAME"]);
        $genders[-1] = "All";
        ksort($genders);
        $genderFormElement->setValueOptions($genders);
        $genderFormElement->setAttributes(["id" => "genderId", "class" => "form-control", "data-init-plugin" => "select2"]);
        $genderFormElement->setLabel("Gender");

        return Helper::addFlashMessagesToArray($this, [
                    'leaveFormElement' => $leaveFormElement,
                    'genderFormElement' => $genderFormElement,
                    'form' => $this->excelImportForm,
                    'searchValues' => AppEntityHelper::getSearchData($this->adapter)
        ]);
    }

    public function importAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        $leaveRepository = new LeaveMasterRepository($this->adapter);
        $leaveBalanceRepository = new LeaveBalanceRepository($this->adapter);

        if ($request->isPost()) {
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
            $this->excelImportForm->setData($post);

            if ($this->excelImportForm->isValid()) {
                $data = $this->excelImportForm->getData();
                $files = $request->getFiles()->toArray();

                $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                $unique = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;
                $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/" . $newFileName);

                $file = Helper::UPLOAD_DIR . "/" . $newFileName;

                $objPHPExcel = PHPExcel_IOFactory::load($file);
                $dataArr = array();

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $worksheetTitle = $worksheet->getTitle();
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

                    for ($row = 2; $row <= $highestRow; ++$row) {
                        for ($col = 0; $col < $highestColumnIndex; ++$col) {
                            $cell = $worksheet->getCellByColumnAndRow($col, $row);
                            $val = $cell->getValue();
                            $dataArr[$row][$col] = $val;
                        }
                    }
                }

                foreach ($dataArr as $row) {
                    $employeeId = $row[0];
                    $leaveId = $row[1];
                    $preYrBalance = $row[2];
                    $totalDays = $row[3];
                    $balance = $preYrBalance + $totalDays;

                    $leaveDetail = $leaveRepository->fetchById($leaveId);
                    if ($leaveDetail['STATUS'] == 'E') {
                        $empLeaveBalanceDetail = $leaveBalanceRepository->getByEmpIdLeaveId($employeeId, $leaveId);
                        if ($empLeaveBalanceDetail == NULL) {
                            $leaveAssign = new \LeaveManagement\Model\LeaveAssign();
                            //$leaveAssign->employeeLeaveAssignId = ((int) Helper::getMaxId($this->adapter, LeaveAssignController::TABLE_NAME, LeaveAssignController::EMPLOYEE_LEAVE_ASSIGN_ID)) + 1;
                            $leaveAssign->createdDt = Helper::getcurrentExpressionDate();
                            $leaveAssign->createdBy = $this->employeeId;
                            $leaveAssign->employeeId = $employeeId;
                            $leaveAssign->leaveId = $leaveId;
                            $leaveAssign->totalDays = $totalDays;
                            $leaveAssign->previousYearBalance = $preYrBalance;
                            $leaveAssign->balance = $balance;
                            $this->repository->add($leaveAssign);
                        } else {
                            if ($leaveDetail['CARRY_FORWARD'] == 'Y') {
                                $updatePreYrBalance = $this->repository->updatePreYrBalance($employeeId, $leaveId, $preYrBalance, $totalDays, $balance);
                            }
                        }
                    }
                }
                unlink(Helper::UPLOAD_DIR . "/" . $newFileName);
            }

            $viewModel = new ViewModel(['data' => ['success' => true]]);
            $viewModel->setTerminal(true);
            $viewModel->setTemplate('layout/json');
            return $viewModel;
        }
    }

}
