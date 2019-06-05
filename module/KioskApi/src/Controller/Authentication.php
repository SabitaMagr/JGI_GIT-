<?php

namespace KioskApi\Controller;

use Exception;
use KioskApi\Repository\AuthenticationRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class Authentication extends AbstractActionController {

    private $adapter;
    private $thumbId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function statusAction() {

        try {
            $request = $this->getRequest();

            $this->thumbId = $request->getHeader('Thumb-Id')->getFieldValue();

            $requestType = $request->getMethod();

            $responseData = [];

            switch ($requestType) {
                case Request::METHOD_GET:
                    $responseData = $this->getStatus($this->thumbId);
                    if ($responseData == NULL) {
                        return new JsonModel(['success' => false, 'data' => 'no data', 'message' => 'No record found']);
                    }
                    break;
                default :
                    throw new Exception('The request is unknown');
            }
            return new JsonModel(['success' => true, 'data' => $responseData, 'message' => $requestType]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => $responseData, 'message' => $e->getMessage()]);
        }
    }

    private function getStatus($thumbId) {
        $statusRepo = new AuthenticationRepository($this->adapter);

        return $statusRepo->fetchEmployeeData($thumbId);
    }

}
