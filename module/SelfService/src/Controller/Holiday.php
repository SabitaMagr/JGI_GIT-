<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/29/16
 * Time: 12:36 PM
 */
namespace SelfService\Controller;

use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use SelfService\Repository\HolidayRepository;
use Application\Helper\Helper;

class Holiday extends AbstractActionController
{
    private $authService;
    private $user_id;
    private $employee_id;
    private $holidayRepository;

    public function __construct(AdapterInterface $adapter)
    {
        $this->holidayRepository = new HolidayRepository($adapter);

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->user_id = $recordDetail['user_id'];
        $this->employee_id = $recordDetail['employee_id'];
    }

    public function indexAction()
    {
        $holidays = $this->holidayRepository->selectAll($this->employee_id);
        return Helper::addFlashMessagesToArray($this, ['holidays' => $holidays]);
    }
}