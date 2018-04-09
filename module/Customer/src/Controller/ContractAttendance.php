<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Customer\Model\ContractAttendanceModel;
use Customer\Model\Customer;
use Customer\Repository\ContractAttendanceRepo;
use Customer\Repository\CustContractEmpRepo;
use Customer\Repository\CustomerContractRepo;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Setup\Model\HrEmployees;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\View\Model\JsonModel;
use function Zend\Filter\File\move_uploaded_file;

class ContractAttendance extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(ContractAttendanceRepo::class);
//        $this->initializeForm(WagedEmployeeSetupForm::class);
    }

    public function indexAction() {

        $monthList = $this->repository->getMonthList();

        $employeeList = $this->repository->getEmployeeListWithCode();


        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    'customerList' => EntityHelper::getTableList($this->adapter, Customer::TABLE_NAME, [Customer::CUSTOMER_ID, Customer::CUSTOMER_ENAME], [Customer::STATUS => "E"]),
                    'employeeList' => $employeeList,
                    'monthList' => $monthList
        ]);
    }

    public function pullCustomerMonthlyAttendanceAction() {
        try {
            $request = $this->getRequest();
            $customerId = $request->getPost('customerId');
            $monthId = $request->getPost('monthId');

            $attendnaceDetails = $this->repository->getCutomerEmpAttendnaceMonthly($monthId, $customerId);
            return new JsonModel(['success' => true, 'data' => $attendnaceDetails, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute("contract-attendance");
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchById($id);
                $list = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        $customerContractRepo = new CustomerContractRepo($this->adapter);
        $customerContractDetails = $customerContractRepo->fetchById($id);

        $contractStartDate = $customerContractDetails['START_DATE'];
        $contractEndDate = $customerContractDetails['END_DATE'];


        $customerEmployeeRepository = new CustContractEmpRepo($this->adapter);

        $monthDetails = $customerEmployeeRepository->getAllMonthBetweenTwoDates($contractStartDate, $contractEndDate);


        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'monthDetails' => $monthDetails
        ]);
    }

    public function exportTemplateAction() {


        try {
            $id = (int) $this->params()->fromRoute("id");
            $monthId = (int) $this->params()->fromRoute("monthId");
            if ($id === 0) {
                throw new Exception("id is not passed");
            }
            if ($monthId === 0) {
                throw new Exception("monthId is not passed");
            }


            $attendanceData = $this->repository->fetchContractAttendanceMonthWise($id, $monthId);


            $contractRepo = new CustomerContractRepo($this->adapter);
            $contractDetails = $contractRepo->fetchById($id);

            $cutomerName = $contractDetails['CUSTOMER_ENAME'];
            $contractName = $contractDetails['CONTRACT_NAME'];

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();



            $sheet->setCellValue('A1', 'Customer')
                    ->setCellValue('B1', 'Contract Name')
                    ->setCellValue('C1', 'CONTRACT_ID')
                    ->setCellValue('D1', 'MONTH_ID')
                    ->setCellValue('E1', 'EMPLOYEE_ID')
                    ->setCellValue('F1', 'EMPLOYEE_NAME')
                    ->setCellValue('G1', 'ATTENDNACE_DATE')
                    ->setCellValue('H1', 'ATTENDANCE_IN_TIME')
                    ->setCellValue('I1', 'ATTENDANCE_OUT_TIME')
                    ->setCellValue('J1', 'Normal Hour')
                    ->setCellValue('K1', 'PartTime Hour')
                    ->setCellValue('L1', 'OverTime Hour')
                    ->setCellValue('M1', 'Absent')
                    ->setCellValue('N1', 'Substitute');



            $i = 2;
            foreach ($attendanceData as $exportData) {
                $customerNameSheet = 'A' . $i;
                $contractNameSheet = 'B' . $i;
                $contractSheet = 'C' . $i;
                $monthIdSheet = 'D' . $i;
                $employeeIdSheet = 'E' . $i;
                $employeeNameSheet = 'F' . $i;
                $dateSheet = 'G' . $i;
                $inTimeSheet = 'H' . $i;
                $outTimeSheet = 'I' . $i;
                $sheet->setCellValue($customerNameSheet, $cutomerName)
                        ->setCellValue($contractNameSheet, $contractName)
                        ->setCellValue($contractSheet, $exportData['CONTRACT_ID'])
                        ->setCellValue($monthIdSheet, $monthId)
                        ->setCellValue($employeeIdSheet, $exportData['EMPLOYEE_ID'])
                        ->setCellValue($employeeNameSheet, $exportData['FULL_NAME'])
                        ->setCellValue($dateSheet, $exportData['ATTENDANCE_DT'])
                        ->setCellValue($inTimeSheet, $exportData['IN_TIME'])
                        ->setCellValue($outTimeSheet, $exportData['OUT_TIME']);
                $i++;
            }

            // Redirect output to a client’s web browser (Xlsx)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="attendanceFile.xlsx"');
            header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit();
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    function validateDate($date, $format = 'd-M-Y') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function uploadAttendanceAction() {


        try {

            $id = (int) $this->params()->fromRoute("id");
            if ($id === 0) {
                throw new Exception("id is not passed");
            }


            $request = $this->getRequest();
            $files = $request->getFiles()->toArray();

            $postData = $request->getPost();
            $monthId = $request->getPost('monthId');

//            echo '<pre>';
//            print_r($monthId);
//            die();

            if (sizeof($files) > 0) {
                $ext = pathinfo($files['excel_file']['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($files['excel_file']['name'], PATHINFO_FILENAME);
                $unique = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;
                $uploadPath = Helper::UPLOAD_DIR . "/attendance/" . $newFileName;


                if ($ext != 'xlsx') {
                    throw new Exception("Please upload a xlsx file");
                }


                if (!empty($_FILES["excel_file"])) {
                    $success = move_uploaded_file($files['excel_file']['tmp_name'], $uploadPath);
                    if (!$success) {
                        throw new Exception("Upload unsuccessful.");
                    }

                    $reader = new Xlsx();
                    $spreadsheet = $reader->load($uploadPath);
                    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);


                    $contractAttendnaceModel = new ContractAttendanceModel();
                    $i = 0;


                    foreach ($sheetData as $importDetails) {

                        $contractId = $importDetails['C'];
                        $monthIdFromUpload = $importDetails['D'];
                        $employeeId = $importDetails['E'];
                        $attendanceDate = $importDetails['G'];
                        $inTime = $importDetails['H'];
                        $outTime = $importDetails['I'];
                        $normalHour = $importDetails['J'];
                        $ptHour = $importDetails['K'];
                        $otHour = $importDetails['L'];
                        $absent = $importDetails['M'];
                        $substitute = $importDetails['N'];
                        if ($i != 0) {
//                            ECHO $contractId
//                            echo '<pre>';
//                            print_r($importDetails);
//                            die();
                            if ($contractId != $id) {
                                throw new Exception('contract Id mismatch data in excel');
                            }

                            if (!is_numeric($employeeId)) {
                                throw new Exception('employee_id  data error');
                            }

                            if (!is_numeric($employeeId)) {
                                throw new Exception('employee_id  data error');
                            }

                            if ($monthId != $monthIdFromUpload) {
                                throw new Exception('MOnth_id is not valid');
                            }

                            $contractAttendnaceModel->inTime = new Expression("TO_TIMESTAMP('{$inTime}', 'HH.MI AM')");
                            $contractAttendnaceModel->outTime = new Expression("TO_TIMESTAMP('{$outTime}', 'HH.MI AM')");
                            $contractAttendnaceModel->outTime = new Expression("TO_TIMESTAMP('{$outTime}', 'HH.MI AM')");
                            $contractAttendnaceModel->normalHour = $normalHour * 60;
                            $contractAttendnaceModel->ptHour = $ptHour * 60;
                            $contractAttendnaceModel->otHour = $otHour * 60;
                            $contractAttendnaceModel->totalHour = $contractAttendnaceModel->normalHour +
                                    $contractAttendnaceModel->ptHour +
                                    $contractAttendnaceModel->otHour;
                            $contractAttendnaceModel->isAbsent = $absent;



                            if ($substitute == 'Y') {
                                $contractAttendnaceModel->contractId = $contractId;
                                $contractAttendnaceModel->monthCodeId = $monthId;
                                $contractAttendnaceModel->employeeId = $employeeId;
                                $contractAttendnaceModel->attendanceDt = $attendanceDate;
                                $contractAttendnaceModel->isSubstitute = $substitute;
                                $this->repository->add($contractAttendnaceModel);
                            } else {
                                $this->repository->updateImportAttendance($contractAttendnaceModel, $id, $monthId, $employeeId, $attendanceDate);
                            }


//                            if ($inTime && $outTime) {
//                                $contractAttendnaceModel->inTime = new Expression("TO_TIMESTAMP('{$inTime}', 'HH.MI AM')");
//                                $contractAttendnaceModel->outTime = new Expression("TO_TIMESTAMP('{$outTime}', 'HH.MI AM')");
////                                $contractAttendnaceModel->totalHour = new Expression("(SELECT round(((TO_DATE('{$outTime}','HH:MI AM')-TO_DATE('{$inTime}','HH:MI AM')) *(60*24)),2) AS minu FROM dual)");
//                            } else {
//                                $contractAttendnaceModel->inTime = null;
//                                $contractAttendnaceModel->outTime = null;
//                                $contractAttendnaceModel->totalHour = null;
//                            }

                            if ($i == 1) {
                                $deleteCondition = [
                                    'CONTRACT_ID' => $contractId,
                                    'MONTH_CODE_ID' => $monthId,
                                    'IS_SUBSTITUTE' => 'Y'
                                ];
                                $this->repository->deleteSubEmplooyee($deleteCondition);
                            }
                        }
                        $i++;
                    }
                }
            } else {
                throw new Exception('Error Reading File');
            }

            $this->flashmessenger()->addMessage("Sucessfully Uploaded.");
            return $this->redirect()->toRoute("contract-attendance", ["action" => "view", "id" => $id]);
        } catch (Exception $e) {
            $errorMSg = $e->getMessage();
            $this->flashmessenger()->addMessage("Error !!" . $errorMSg);
            return $this->redirect()->toRoute("contract-attendance", ["action" => "view", "id" => $id]);
        }
    }

    public function pullAttendanceContractMonthlyAction() {
        try {
            $id = (int) $this->params()->fromRoute("id");
            if ($id === 0) {
                throw new Exception('id is undefined');
            }
            $request = $this->getRequest();
            $postData = $request->getPost();
            $monthId = $request->getPost('monthId');

            $attendanceData = $this->repository->fetchContractAttendanceMonthWise($id, $monthId);

            return new JsonModel(['success' => true, 'data' => $attendanceData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function updateEmpContractAttendnaceAction() {
        $request = $this->getRequest();

        try {

//            $monthCount= Helper::
//            echo '<Pre>';
//            print_r($request->getPost());
//            die();

            $customerId = $request->getPost('customerId');
            $monthId = $request->getPost('monthId');
            $kendoData = $request->getPost('kendoData');

            $monthStartDate = $kendoData['FROM_DATE'];

            $contractId = $kendoData['CONTRACT_ID'];
            $employeeId = $kendoData['EMPLOYEE_ID'];
            $locationId = $kendoData['LOCATION_ID'];
            $shiftId = $kendoData['SHIFT_ID'];

            $sql = "BEGIN
                    DELETE FROM HRIS_CONTRACT_EMP_ATTENDANCE WHERE
 MONTH_CODE={$monthId} AND CONTRACT_ID={$contractId} AND EMPLOYEE_ID={$employeeId} 
AND LOCATION_ID={$locationId} AND SHIFT_ID={$shiftId};";

            for ($i = 1; $i <= 32; $i++) {
                $arrayIndex = 'C' . $i;
                $attendnaceStatus = $kendoData[$arrayIndex];
                if ($attendnaceStatus != 'PR') {
                    $sql .= "INSERT INTO HRIS_CONTRACT_EMP_ATTENDANCE 
                    (ATTENDANCE_DATE,EMPLOYEE_ID,CUSTOMER_ID,CONTRACT_ID,
                    LOCATION_ID,SHIFT_ID,MONTH_CODE,STATUS)
                    VALUES
                    (TO_DATE('{$monthStartDate}','DD-MON-YY')+{$i}-1,{$employeeId},{$customerId},{$contractId},
                    {$locationId},{$shiftId},{$monthId},'{$attendnaceStatus}');";
                }
//                echo $attendnaceStatus;
            }

            $sql .= "END;";



            $result = $this->repository->updateAttendance($sql);
            return new JsonModel(['success' => true, 'data' => [], 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function billPrintAction() {

        $monthList = $this->repository->getMonthList();

        return Helper::addFlashMessagesToArray($this, [
                    'customerList' => EntityHelper::getTableList($this->adapter, Customer::TABLE_NAME, [Customer::CUSTOMER_ID, Customer::CUSTOMER_ENAME], [Customer::STATUS => "E"]),
                    'monthList' => $monthList
        ]);
    }

    public function pullMonthlyBillCustomerWiseAction() {
        try {
            $request = $this->getRequest();
            $customerId = $request->getPost('customerId');
            $monthId = $request->getPost('monthId');




            $returnData['attendnaceDetails'] = $this->repository->pullMonthlyBillCustomerWise($monthId, $customerId);


//            $attendnaceDetails = $this->repository->pullMonthlyBillCustomerWise($monthId, $customerId);
            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function pullAttendanceAbsentDataAction() {
        try {
            $request = $this->getRequest();

            $monthStartDate = $request->getPost('monthStartDate');
            $column = $request->getPost('column');
            $customerId = $request->getPost('customerId');
            $contractId = $request->getPost('contractId');
            $employeeId = $request->getPost('employeeId');
            $locationId = $request->getPost('locationId');
            $dutyTypeId = $request->getPost('dutyTypeId');
            $designationId = $request->getPost('designationId');
            $empAssignId = $request->getPost('empAssignId');
            $startTime = $request->getPost('startTime');
            $endTime = $request->getPost('endTime');


            $returnData = $this->repository->pullAttendanceAbsentData($monthStartDate, $column, $customerId, $contractId, $employeeId, $locationId, $dutyTypeId, $designationId, $startTime, $endTime
                    , $empAssignId);
            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function updateAttendanceDataAction() {
        try {
            $request = $this->getRequest();
            $postData = $request->getPost();


//            echo'<pre>';
//            print_r($postData);
//            die();


            $attendanceDate = $request->getPost('attendanceDate');
            $customerId = $request->getPost('customerId');
            $contractId = $request->getPost('contractId');
            $employeeId = $request->getPost('employeeId');
            $locationId = $request->getPost('locationId');
            $dutyTypeId = $request->getPost('dutyTypeId');
            $designationId = $request->getPost('designationId');
            $empAssignId = $request->getPost('empAssignId');
            $status = $request->getPost('stauts');
            $normalHour = $request->getPost('normalHour');
            $otHour = $request->getPost('otHour');
            $subEmployeeId = $request->getPost('subEmployeeId');
            $postingType = $request->getPost('postingType');


            $returnData = $this->repository->updateAttendanceData($attendanceDate, $customerId, $contractId, $employeeId, $locationId, $dutyTypeId, $designationId, $empAssignId, $status, $normalHour, $otHour, $subEmployeeId, $postingType);

            return new JsonModel(['success' => true, 'data' => $returnData, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
