<?php
namespace WorkOnHoliday\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;

class WorkOnHolidayStatus extends AbstractActionController{
    public function __construct() {
        
    }
    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['list'=>"hellow holiday status"]);
    }
}