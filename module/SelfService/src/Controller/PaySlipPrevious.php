<?php

namespace SelfService\Controller;

use Application\Controller\HrisController;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\ViewModel;

class PaySlipPrevious extends HrisController {

    private $viewType;
    private $queryToList;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->viewType = $this->storageData['preference']['oldPayslipType'];

        $this->queryToList = function($sql) {
            $statement = $this->adapter->query($sql);
            $iterator = $statement->execute();
            return iterator_to_array($iterator);
        };
    }

    public function payslipAction() {
        $template = "";
        switch ($this->viewType) {
            case "M":
                $template = "mysql/payslip";
                break;
            case "O":
                $template = "oracle/payslip";
                break;
            case "N":
                print "Not Available.";
                exit;
                break;
        }
        $view = new ViewModel($this->stickFlashMessagesTo(
                        [
                            'employeeId' => $this->employeeId,
                            'employeeCode' => $this->storageData['employee_detail']['EMPLOYEE_CODE'],
                            'queryToList' => $this->queryToList
        ]));
        $view->setTemplate($template);
        return $view;
    }

    public function printPayslipAction() {
        $template = "";
        switch ($this->viewType) {
            case "M":
                $template = "mysql/print-payslip";
                break;
            case "O":
                $template = "oracle/print-payslip";
                break;
            case "N":
                print "Not Available.";
                exit;
                break;
        }
        $employeeid = $this->params()->fromRoute('id');
        $mcode = $this->params()->fromRoute('mcode');
        $view = new ViewModel($this->stickFlashMessagesTo(['employeeId' => $employeeid, 'mcode' => $mcode, 'adapter' => $this->adapter]));
        $view->setTemplate($template);
        return $view;
    }

    public function taxsheetAction() {
        $template = "";
        switch ($this->viewType) {
            case "M":
                $template = "mysql/taxsheet";
                break;
            case "O":
                $template = "oracle/taxsheet";
                break;
            case "N":
                print "Not Available.";
                exit;
                break;
        }
        $view = new ViewModel($this->stickFlashMessagesTo(['employeeId' => $this->employeeId, 'employeeCode' => $this->storageData['employee_detail']['EMPLOYEE_CODE'], 'adapter' => $this->adapter]));
        $view->setTemplate($template);
        return $view;
    }

}
