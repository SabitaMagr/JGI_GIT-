<?php

namespace Payroll\Controller;

use Application\Controller\HrisController;
use Exception;
use Payroll\Repository\TaxSheetRepo;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class TaxSheetController extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(TaxSheetRepo::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $postedData = $request->getPost();
                $data = $this->repository->fetchEmployeeTaxSheet($postedData['monthId'], $postedData['employeeId']);
                return new JsonModel(['success' => true, 'data' => $data, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
    }

}
