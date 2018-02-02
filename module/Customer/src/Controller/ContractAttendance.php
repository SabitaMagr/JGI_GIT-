<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Customer\Model\ContractAttendanceModel;
use Customer\Repository\ContractAttendanceRepo;
use Customer\Repository\CustContractEmpRepo;
use Customer\Repository\CustomerContractRepo;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\View\Model\JsonModel;

class ContractAttendance extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(ContractAttendanceRepo::class);
//        $this->initializeForm(WagedEmployeeSetupForm::class);
    }

    public function indexAction() {

        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $customerRepo = new CustomerContractRepo($this->adapter);
                $result = $customerRepo->fetchAll();
                $list = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl
        ]);
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
                    ->setCellValue('D1', 'EMPLOYEE_ID')
                    ->setCellValue('E1', 'EMPLOYEE_NAME')
                    ->setCellValue('F1', 'ATTENDNACE_DATE')
                    ->setCellValue('G1', 'ATTENDANCE_IN_TIME')
                    ->setCellValue('H1', 'ATTENDANCE_OUT_TIME')
                    ->setCellValue('I1', 'Normal Hour')
                    ->setCellValue('J1', 'PartTime Hour')
                    ->setCellValue('K1', 'OverTime Hour')
                    ->setCellValue('L1', 'Absent')
                    ->setCellValue('M1', 'Substitute');



            $i = 2;
            foreach ($attendanceData as $exportData) {
                $customerNameSheet = 'A' . $i;
                $contractNameSheet = 'B' . $i;
                $contractSheet = 'C' . $i;
                $employeeIdSheet = 'D' . $i;
                $employeeNameSheet = 'E' . $i;
                $dateSheet = 'F' . $i;
                $inTimeSheet = 'G' . $i;
                $outTimeSheet = 'H' . $i;
                $sheet->setCellValue($customerNameSheet, $cutomerName)
                        ->setCellValue($contractNameSheet, $contractName)
                        ->setCellValue($contractSheet, $exportData['CONTRACT_ID'])
                        ->setCellValue($employeeIdSheet, $exportData['EMPLOYEE_ID'])
                        ->setCellValue($employeeNameSheet, $exportData['FULL_NAME'])
                        ->setCellValue($dateSheet, $exportData['ATTENDANCE_DT'])
                        ->setCellValue($inTimeSheet, '')
                        ->setCellValue($outTimeSheet, '');
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

            $postData=$request->getPost();
            $monthId=$request->getPost('monthId');
            
            echo '<pre>';
            print_r($monthId);
            die();

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
                        
                        echo '<pre>';
                        print_r($importDetails);
                        die();
                        $contractId = $importDetails['A'];
                        $employeeId = $importDetails['B'];
                        $attendanceDate = $importDetails['D'];
                        $inTime = $importDetails['E'];
                        $outTime = $importDetails['F'];
//                        $normalHour = $importDetails['G'];
//                        $ptHour = $importDetails['G'];
//                        $otHour = $importDetails['I'];
                        if ($i != 0) {
                            if ($contractId != $id && !is_numeric($employeeId)) {
                                throw new Exception('contract Id mismatch data in excel');
                            }

                            if ($this->validateDate($attendanceDate) == false) {
                                throw new Exception('some dates are invalid in excel file');
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
                            $this->repository->updateImportAttendance($contractAttendnaceModel, $id, $employeeId, $attendanceDate);
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

}
