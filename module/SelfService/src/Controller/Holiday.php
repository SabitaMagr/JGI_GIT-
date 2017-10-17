<?php

namespace SelfService\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Exception;
use SelfService\Repository\HolidayRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class Holiday extends AbstractActionController {

    private $authService;
    private $user_id;
    private $employee_id;
    private $holidayRepository;

    public function __construct(AdapterInterface $adapter) {
        $this->holidayRepository = new HolidayRepository($adapter);

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->user_id = $recordDetail['user_id'];
        $this->employee_id = $recordDetail['employee_id'];
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $holidayList = $this->holidayRepository->selectAll($this->employee_id);
                $holidays = [];
                $getValue = function($halfDay) {
                    if ($halfDay == "F") {
                        return "First Half";
                    } else if ($halfDay == "S") {
                        return "Second Half";
                    } else if ($halfDay == "N") {
                        return "Full Day";
                    }
                };
                foreach ($holidayList as $holidayRow) {
                    $new_row = array_merge($holidayRow, ['HALF_DAY' => $getValue($holidayRow['HALFDAY'])]);
                    unset($holidayRow['HALFDAY']);
                    array_push($holidays, $new_row);
                }

                return new CustomViewModel(['success' => true, 'data' => $holidays, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, []);
    }

}
