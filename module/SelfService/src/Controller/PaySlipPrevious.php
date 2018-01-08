<?php

namespace SelfService\Controller;

use Application\Controller\HrisController;

class PaySlipPrevious extends HrisController {

    public function payslipAction() {
        return $this->stickFlashMessagesTo(['employeeId' => $this->employeeId, 'employeeCode' => $this->employeeCode]);
    }

    public function printPayslipAction() {
        $employeeid = $this->params()->fromRoute('id');
        $mcode = $this->params()->fromRoute('mcode');
        return $this->stickFlashMessagesTo(['employeeId' => $employeeid, 'mcode' => $mcode]);
    }

    public function taxsheetAction() {
        return $this->stickFlashMessagesTo(['employeeId' => $this->employeeId, 'employeeCode' => $this->employeeCode]);
    }

}
