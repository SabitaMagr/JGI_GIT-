<?php
namespace Overtime\Controller;

use Application\Controller\HrisController;
use Application\Helper\EntityHelper;
use Exception;
use Overtime\Repository\OvertimeReportRepo;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;

class OvertimeReport extends HrisController {

    public function __construct(AdapterInterface $adapter, StorageInterface $storage, OvertimeReportRepo $repository) {
        parent::__construct($adapter, $storage);
        $this->repository = $repository;
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $list = $this->repository->fetch($data['monthId']);
                $columnList = $this->repository->fetchColumns($data['monthId']);
                return new JsonModel(["success" => "true", "data" => ['columnList' => $columnList, 'gridData' => $list]]);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
            }
        }
        return $this->stickFlashMessagesTo([
                'searchValues' => EntityHelper::getSearchData($this->adapter),
                'acl' => $this->acl,
                'employeeDetail' => $this->storageData['employee_detail'],
                'preference' => $this->preference
        ]);
    }
}
