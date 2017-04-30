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
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;

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
            $salaryDetail->salaryDetailId = ((int) Helper::getMaxId($this->adapter, SalaryDetail::TABLE_NAME, SalaryDetail::SALARY_DETAIL_ID)) + 1;
            if (isset($postedData['jobHistoryId'])) {
                $salaryDetail->jobHistoryId = $postedData['jobHistoryId'];
            }

            $successFlag = $this->repo->add($salaryDetail);
            try {
                HeadNotification::pushNotification( NotificationEvents::SALARY_REVIEW , $salaryDetail, $this->adapter, $this->plugin('url'));
            } catch (Exception $e) {
                $this->flashmessenger()->addMessage($e->getMessage());
            }
            if ($successFlag) {
                $this->flashmessenger()->addMessage("SalaryReview Successfully Added!!!");
                return $this->redirect()->toRoute("salaryReview");
            } else {
                print("Failure");
                exit;
            }
        }


        $employeeList = EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => 'E', HrEmployees::RETIRED_FLAG => 'N'], HrEmployees::FIRST_NAME,"ASC", " ", false,true);
        $employeeSelect = new Select();
        $employeeSelect->setName("employeeId");
        $employeeSelect->setValueOptions($employeeList);
        $employeeSelect->setAttributes(["id" => "employeeId", "class" => "form-control"]);
        $employeeSelect->setLabel("Employee");

        $monthRepo = new MonthRepository($this->adapter);
        $currentMonth = $monthRepo->fetchByDate(Helper::getcurrentExpressionDate());

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

        $salaryDetail = $this->repo->fetchById($id);
//        print "<pre>";
//        print_r($salaryDetail);
//        exit;
        $employeeList = EntityHelper::getTableKVListWithSortOption($this->adapter, HrEmployees::TABLE_NAME, HrEmployees::EMPLOYEE_ID, [HrEmployees::FIRST_NAME, HrEmployees::MIDDLE_NAME, HrEmployees::LAST_NAME], [HrEmployees::STATUS => 'E', HrEmployees::RETIRED_FLAG => 'N'], HrEmployees::FIRST_NAME,"ASC", " ", false,true);
        $employeeSelect = new Select();
        $employeeSelect->setName("employeeId");
        $employeeSelect->setValue($salaryDetail[SalaryDetail::EMPLOYEE_ID]);
        $employeeSelect->setValueOptions($employeeList);
        $employeeSelect->setAttributes(["id" => "employeeId", "class" => "form-control", "disabled" => "disabled"]);
        $employeeSelect->setLabel("Employee");

        return Helper::addFlashMessagesToArray($this, [
                    'employeeElement' => $employeeSelect,
                    'salaryDetail' => $salaryDetail,
                    'id' => $id
        ]);
    }

    public function lastReviewDateAction() {
        $request = $this->getRequest();
        $data = [];
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $previousReview = $this->repo->fetchIfAvailable(Helper::getExpressionDate($postedData['fromDate']), Helper::getExpressionDate($postedData['toDate']), $postedData['employeeId']);
            $data = ['lastReviewDateThisMonth' => $previousReview->current()];
        } else {
            $data = [];
        }

        $view = new ViewModel(['data' => $data]);
        $view->setTerminal(true);
        $view->setTemplate('layout/json');
        return $view;
    }

}
