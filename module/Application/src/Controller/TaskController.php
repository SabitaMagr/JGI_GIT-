<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;


class TaskController extends AbstractActionController
{
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }
    
    
    public function indexAction() {
       echo 'i am index';
       die();
    }
    
    
    public function addAction(){
        echo'm here';
        die();
         try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data='the request is post';
                retrun[$data];
            } else {
                throw new Exception("The request should be of type post");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
    
    
    
}