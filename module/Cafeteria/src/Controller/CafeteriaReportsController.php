<?php
namespace Cafeteria\Controller;

use Application\Controller\HrisController;
use Cafeteria\Repository\CafeteriaReports;
use Application\Helper\EntityHelper as ApplicationHelper;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;

class CafeteriaReportsController extends HrisController{
    
    protected $adapter;
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        parent::__construct($adapter, $storage);
        $this->initializeRepository(CafeteriaReports::class);
    }

    public function indexAction(){
        
    }
    
    public function employeeWiseReportAction(){
        
        return $this->stickFlashMessagesTo([
                'searchValues' => ApplicationHelper::getSearchData($this->adapter),
                'acl' => $this->acl,
                'employeeDetail' => $this->storageData['employee_detail'],
                
        ]);
    }
}