<?php

namespace Customer\Controller;

use Application\Controller\HrisController;
use Application\Helper\Helper;
use Customer\Repository\CustomerLocationRepo;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class CustomerLocation extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(CustomerLocationRepo::class);
    }

    public function indexAction() {
        
    }

    public function viewAction() {
        $id = (int) $this->params()->fromRoute("id", -1);
        if ($id === -1) {
            return $this->redirect()->toRoute('customer-setup');
        }
        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl,
                    "id" => $id
        ]);
    }

    public function fetchAllCustomerLocationAction() {
        try {
            $id = (int) $this->params()->fromRoute("id", -1);
            
            $result = $this->repository->fetchAllLocationByCustomer($id);
            $list = Helper::extractDbData($result);
            return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}
