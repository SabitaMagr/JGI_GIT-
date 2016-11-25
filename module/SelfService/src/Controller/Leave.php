<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/29/16
 * Time: 12:46 PM
 */

namespace SelfService\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use SelfService\Repository\LeaveRepository;
use Application\Helper\Helper;

class Leave extends AbstractActionController
{
    private $authService;
    private $user_id;
    private $employee_id;
    private $leaveRepository;

    public function __construct(AdapterInterface $adapter)
    {
        $this->leaveRepository = new LeaveRepository($adapter);

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->user_id = $recordDetail['user_id'];
        $this->employee_id = $recordDetail['employee_id'];
    }

    public function indexAction()
    {
        $leaveList = $this->leaveRepository->selectAll($this->employee_id);
        $leaves = [];
        foreach($leaveList as $leaveRow){
            $leaveTaken =  $leaveRow['TOTAL_DAYS']-$leaveRow['BALANCE'];
            $new_row = array_merge($leaveRow,['LEAVE_TAKEN'=>$leaveTaken]);
            array_push($leaves, $new_row);
        }
        return Helper::addFlashMessagesToArray($this, ['leaves' => $leaves]);
    }
}