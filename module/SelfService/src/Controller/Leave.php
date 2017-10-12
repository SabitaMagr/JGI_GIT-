<?php

/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/29/16
 * Time: 12:46 PM
 */

namespace SelfService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Exception;
use SelfService\Repository\LeaveRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class Leave extends AbstractActionController {

    private $authService;
    private $employee_id;
    private $leaveRepository;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->leaveRepository = new LeaveRepository($adapter);

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->storageData = $storage->read();
        $this->employee_id = $this->storageData['employee_id'];
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $leaveList = $this->leaveRepository->selectAll($this->employee_id);
                $leaves = [];
                foreach ($leaveList as $leaveRow) {
                    $allTotalDays = $leaveRow['PREVIOUS_YEAR_BAL'] + $leaveRow['TOTAL_DAYS'];
                    $leaveTaken = $allTotalDays - $leaveRow['BALANCE'];
                    $new_row = array_merge($leaveRow, ['LEAVE_TAKEN' => $leaveTaken, 'ALL_TOTAL_DAYS' => $allTotalDays]);
                    array_push($leaves, $new_row);
                }
                return new CustomViewModel(['success' => true, 'data' => $leaves, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, []);
    }

}
