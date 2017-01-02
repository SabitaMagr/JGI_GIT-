<?php

namespace ManagerService\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Repository\MonthRepository;
use ManagerService\Model\SalaryDetail;
use ManagerService\Repository\SalaryDetailRepo;
use Setup\Model\HrEmployees;
use Setup\Repository\JobHistoryRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SalaryReviewController extends AbstractActionController {

    private $adapter;
    private $employeeId;
    private $repo;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repo = new SalaryDetailRepo($this->adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function indexAction() {
        $salaryDetailRepo = new SalaryDetailRepo($this->adapter);
        $salaryDetails = $salaryDetailRepo->fetchAll();
        $salaryDetails = Helper::extractDbData($salaryDetails);
        return Helper::addFlashMessagesToArray($this, [
                    'salaryDetails' => $salaryDetails
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $salaryDetail = new SalaryDetail();
            $salaryDetail->employeeId = $postedData['employeeId'];
            $salaryDetail->oldAmount = $postedData['oldAmount'];
            $salaryDetail->newAmount = $postedData['newAmount'];
            $salaryDetail->effectiveDate = Helper::getExpressionDate($postedData['effectiveDate']);
            $salaryDetail->createdDt = Helper::getcurrentExpressionDate();
            $salaryDetail->createdBy = $this->employeeId;
            $salaryDetail->status = 'E';
            if (isset($postedData['jobHistoryId'])) {
                $salaryDetail->jobHistoryId = $postedData['jobHistoryId'];
            }

            $successFlag = $this->repo->add($salaryDetail);
            if ($successFlag) {
                $this->flashmessenger()->addMessage("SalaryReview Successfully Added!!!");
                return $this->redirect()->toRoute("salaryReview");
            } else {
                print("Failure");
                exit;
            }
        }


        $employeeList = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => 'E', HrEmployees::RETIRED_FLAG => 'N'], null, false);
        $employeeSelect = new Select();
        $employeeSelect->setName("employeeId");
        $employeeSelect->setValueOptions($employeeList);
        $employeeSelect->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeSelect->setLabel("Employee");

        $monthRepo = new MonthRepository($this->adapter);
        $currentMonth = $monthRepo->fetchByDate(Helper::getcurrentExpressionDate());
        print "<pre>";
        print_r($currentMonth);
        exit;
        return Helper::addFlashMessagesToArray($this, [
                    'employeeElement' => $employeeSelect,
                    'currentMonth' => $currentMonth
        ]);
    }

    public function historyAction() {
        $request = $this->getRequest();
        $data = [];
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $jobHistoryRepo = new JobHistoryRepository($this->adapter);
            $jobHistoryList = $jobHistoryRepo->fetchHistoryNotReview($postedData['employeeId']);
            $data = ['jobHistoryList' => $jobHistoryList];
        } else {
            $data = [];
        }

        $view = new ViewModel(['data' => $data]);
        $view->setTerminal(true);
        $view->setTemplate('layout/json');
        return $view;
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");

        if ($id == 0) {
            return $this->redirect()->toRoute("salaryReview");
        }

//        $this->repo->fetchById(SalaryDetail::);

        $employeeList = EntityHelper::getTableKVList($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => 'E', HrEmployees::RETIRED_FLAG => 'N'], null, false);
        $employeeSelect = new Select();
        $employeeSelect->setName("employeeId");
        $employeeSelect->setValueOptions($employeeList);
        $employeeSelect->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeSelect->setLabel("Employee");

        return Helper::addFlashMessagesToArray($this, [
                    'employeeElement' => $employeeSelect
        ]);
    }

    public function lastReviewDateAction() {
        $request = $this->getRequest();
        $data = [];
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $jobHistoryList = $jobHistoryRepo->fetchHistoryNotReview($postedData['employeeId']);


            $data = ['lastReviewDateThisMonth' => $jobHistoryList];
        } else {
            $data = [];
        }

        $view = new ViewModel(['data' => $data]);
        $view->setTerminal(true);
        $view->setTemplate('layout/json');
        return $view;
    }

}
