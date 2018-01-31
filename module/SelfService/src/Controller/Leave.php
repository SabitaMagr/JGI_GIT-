<?php

namespace SelfService\Controller;

use Application\Controller\HrisController;
use Exception;
use SelfService\Repository\LeaveRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class Leave extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(LeaveRepository::class);
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $postData = (array) $request->getPost();
                if (isset($postData['fiscalYearMonthNo'])) {
                    $leaveList = $this->repository->monthlyLeaveStatus($this->employeeId, $postData['fiscalYearMonthNo']);
                } else {
                    $leaveList = $this->repository->selectAll($this->employeeId);
                }
                $leaves = iterator_to_array($leaveList, false);
                return new JsonModel(['success' => true, 'data' => $leaves, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return new ViewModel();
    }

}
