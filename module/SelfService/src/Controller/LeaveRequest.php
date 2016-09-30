<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/29/16
 * Time: 12:46 PM
 */
namespace SelfService\Controller;

use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use SelfService\Repository\LeaveRequestRepository;
use Zend\Authentication\AuthenticationService;
use Application\Helper\Helper;

class LeaveRequest extends AbstractActionController{

    private $leaveRequestRepository;
    private $employeeId;
    private $userId;
    private $authService;

    public function __construct(AdapterInterface $adapter)
    {
        $this->leaveRequestRepository = new LeaveRequestRepository($adapter);

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->user_id = $recordDetail['user_id'];
        $this->employee_id = $recordDetail['employee_id'];
    }

    public function indexAction()
    {
        $leaveRequest = $this->leaveRequestRepository->selectAll($this->employee_id);
        return Helper::addFlashMessagesToArray($this, ['leaveRequest' => $leaveRequest]);
    }
}