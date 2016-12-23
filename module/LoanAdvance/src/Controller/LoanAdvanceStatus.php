<?php
namespace LoanAdvance\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;


class LoanAdvanceStatus extends AbstractActionController
{
    public function __construct() {
        
    }
    
    public function indexAction() {
        return Helper::addFlashMessagesToArray($this, ['list'=>'list']);
    }
    
}