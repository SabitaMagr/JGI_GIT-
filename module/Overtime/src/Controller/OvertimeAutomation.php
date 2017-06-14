<?php

namespace Overtime\Controller;

use Application\Helper\Helper;
use Overtime\Repository\OvertimeAutomationRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class OvertimeAutomation extends AbstractActionController {

    private $adapter;
    private $otAutomationRepo;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->otAutomationRepo = new OvertimeAutomationRepository($adapter);
    }

    public function indexAction() {
        $overtimeCompulsoryList = $this->otAutomationRepo->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['overtimeCompulsoryList' => Helper::extractDbData($overtimeCompulsoryList)]);
    }

}
