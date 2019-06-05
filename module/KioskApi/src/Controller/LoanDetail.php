<?php

namespace KioskApi\Controller;

use Exception;
use KioskApi\Repository\LoanDetailRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class LoanDetail extends AbstractActionController {

    private $adapter;
    private $employeeId;
    private $loanId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function statusAction() {

        try {
            $request = $this->getRequest();

            $this->employeeId = $request->getHeader('Employee-Id')->getFieldValue();
            $this->loanId = $request->getHeader('Loan-Id')->getFieldValue();

            $requestType = $request->getMethod();

            $responseData = [];

            switch ($requestType) {
                case Request::METHOD_GET:
                    $responseData = $this->getStatus($this->employeeId,$this->loanId);
                    if ($responseData == NULL) {
                        return new JsonModel(['success' => false, 'data' => $responseData, 'message' => 'No record found']);
                    }
                    break;
                default :
                    throw new Exception('The request is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseData, 'message' => $requestType]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => $responseData, 'message' => $e->getMessage()]);
        }
    }

    public function getStatus($employeeId,$loanId) {
        $statusRepo = new LoanDetailRepository($this->adapter);
       
        return $statusRepo->fetchLoanDetail($employeeId,$loanId);
    }

}
