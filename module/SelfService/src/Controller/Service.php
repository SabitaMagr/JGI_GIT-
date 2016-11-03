<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/3/16
 * Time: 11:12 AM
 */
namespace SelfService\Controller;

use Application\Helper\Helper;
use SelfService\Repository\ServiceRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Setup\Form\JobHistoryForm;
use Setup\Model\JobHistory;
use Setup\Helper\EntityHelper;
use Application\Helper\EntityHelper as EntityHelper1;

class Service extends AbstractActionController {

    private $adapter;
    private $employeeId;
    private $authService;
    private $form;
    private $repository;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        $this->authService = new AuthenticationService();
        $recordDetail = $this->authService->getIdentity();
        $this->employeeId = $recordDetail['employee_id'];
        $this->repository = new ServiceRepository($adapter);
    }

    public function initializeForm(){
        $jobHistoryForm = new JobHistoryForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($jobHistoryForm);
    }

    public function indexAction()
    {
        return Helper::addFlashMessagesToArray($this,['employeeId'=>$this->employeeId]);
    }
    public function viewAction(){
        $id = (int)$this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('jobHistory');
        }
        $this->initializeForm();
        $request = $this->getRequest();

        $jobHistory = new JobHistory();
        if (!$request->isPost()) {
            $jobHistory->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($jobHistory);
        } else {


        }
        return Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'id' => $id,
                'messages' => $this->flashmessenger()->getMessages(),
                'employees' => EntityHelper1::getTableKVList($this->adapter,"HR_EMPLOYEES","EMPLOYEE_ID",["FIRST_NAME","MIDDLE_NAME","LAST_NAME"],["STATUS"=>"E"]),
                'departments' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_DEPARTMENTS),
                'designations' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_DESIGNATIONS),
                'branches' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_BRANCHES),
                'positions' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_POSITIONS),
                'serviceTypes' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_SERVICE_TYPES),
            ]
        );
    }
}