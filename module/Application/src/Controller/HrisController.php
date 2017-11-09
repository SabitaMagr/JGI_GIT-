<?php

namespace Application\Controller;

use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;

class HrisController extends AbstractActionController {

    protected $adapter;
    protected $employeeId;
    protected $storageData;
    protected $acl;

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->storageData = $storage->read();
        $this->acl = $this->storageData['acl'];
        $this->employeeId = $this->storageData['employee_id'];
    }

    protected function stickFlashMessagesTo($return) {
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $return['messages'] = $flashMessenger->getMessages();
        }
        return $return;
    }

}
