<?php

namespace AttendanceManagement\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\FiscalYear;
use Application\Model\Months;
use AttendanceManagement\Repository\PenaltyRepo;
use Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class Penalty extends AbstractActionController {

    private $adapter;
    private $repo;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repo = new PenaltyRepo($adapter);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $reportData = $this->repo->monthWiseReport($data);
                return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'searchValues' => EntityHelper::getSearchData($this->adapter),
                    'fiscalYears' => EntityHelper::getTableList($this->adapter, FiscalYear::TABLE_NAME, [FiscalYear::FISCAL_YEAR_ID, FiscalYear::FISCAL_YEAR_NAME]),
                    'months' => EntityHelper::getTableList($this->adapter, Months::TABLE_NAME, [Months::MONTH_ID, Months::MONTH_EDESC, Months::FISCAL_YEAR_ID])
        ]);
    }

    public function penaltyDetailWSAction() {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("must be a post request.");
            }
            $data = $request->getPost();
            $reportData = $this->repo->penaltyDetail($data['employeeId'], Helper::getExpressionDate($data['attendanceDt']), $data['type']);
            return new CustomViewModel(['success' => true, 'data' => $reportData, 'error' => '']);
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
