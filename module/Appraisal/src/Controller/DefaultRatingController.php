<?php
namespace Appraisal\Controller;

use Application\Helper\Helper;
use Appraisal\Repository\DefaultRatingRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Barcode\AdapterInterface;

class DefaultRatingController extends AbstractActionController{
    private $adapter;
    private $repository;
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new DefaultRatingRepository();
    }
    public function indexAction(){
        $result = $this->repository->fetchAll();
        $list = [];
        foreach($result as $row){
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
           'defaultRating'=>$list 
        ]);
    }
    public function addAction(){
        
    }
}
