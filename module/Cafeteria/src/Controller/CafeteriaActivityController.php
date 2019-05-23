<?php
namespace Cafeteria\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Cafeteria\Repository\CafeteriaActivity;
use Cafeteria\Repository\CafeteriaMap;
use Zend\Authentication\Storage\StorageInterface;
use Zend\View\Model\JsonModel;

class CafeteriaActivityController extends HrisController{
    
    protected $adapter;
    private $cafeteriaMapRepo;
    
    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        parent::__construct($adapter, $storage);
        $this->initializeRepository(CafeteriaActivity::class);
        $this->cafeteriaMapRepo = new CafeteriaMap($this->adapter);
    }

    public function indexAction(){
        return $this->redirect()->toRoute('cafeteria-activity', array(
            'controller' => 'CafeteriaActivityController',
            'action' =>  'activity'
        ));
    }
    
    public function activityAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $result = $this->repository->getNextLogNo();
            $logNo = Helper::extractDbData($result)[0]['LOG_NO'];
            $this->repository->saveLog($data, $logNo, $this->employeeId);
            $this->repository->saveLogDetails($data, $logNo, $this->employeeId);
            $this->flashmessenger()->addMessage("Record Saved Successfully.");
            return $this->redirect()->toRoute('cafeteria-activity', array(
                'controller' => 'CafeteriaActivityController',
                'action' =>  'activity'
            ));
        }
        
        $result = $this->repository->fetchTimes();
        $timeList = Helper::extractDbData($result);
        
        $result = $this->cafeteriaMapRepo->fetchMenu();
        $menuList = Helper::extractDbData($result);
        
        $mapList = []; 
        for($i = 0; $i < count($timeList); $i++){
            $result = $this->cafeteriaMapRepo->fetchMappingDetailsByTime($timeList[$i]['TIME_ID']);
            $mapList[$timeList[$i]['TIME_NAME']] = Helper::extractDbData($result);
        }
        
        return Helper::addFlashMessagesToArray($this, [
            'timeList' => $timeList,
            'menuList' => $menuList,
            'mapList' => $mapList,
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'acl' => $this->acl,
            'employeeDetail' => $this->storageData['employee_detail']
        ]);
    }
}