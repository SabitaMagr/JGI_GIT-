<?php

namespace Notification\Controller;

use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Notification\Form\NewsForm;
use Notification\Model\NewsModel;
use Notification\Repository\NewsRepository;
use Setup\Model\Company;
use Setup\Repository\CompanyRepository;
use Setup\Repository\DepartmentRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class NewsController extends AbstractActionController {

    private $adapter;
    private $form;
    private $repository;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new NewsRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $form = new NewsForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        $result = $this->repository->fetchAll();
        $list = [];
        foreach ($result as $row) {
            array_push($list, $row);
        }
        return Helper::addFlashMessagesToArray($this, [
                    'news' => $list
        ]);
    }

    public function addAction() {
        $this->initializeForm();
//        $employeeRepo = new EmployeeRepository($this->adapter);
//        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        $request = $this->getRequest();

        $companyRepo = new CompanyRepository($this->adapter);
        $departmentRepo = new DepartmentRepository($this->adapter);
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $newsModel = new NewsModel();
                $newsModel->exchangeArrayFromForm($this->form->getData());
                $newsModel->newsId = ((int) Helper::getMaxId($this->adapter, $newsModel::TABLE_NAME, $newsModel::NEWS_ID)) + 1;
                $newsModel->createdBy = $this->employeeId;
                $newsModel->approvedBy = $this->employeeId;
                $newsModel->createdDt = Helper::getcurrentExpressionDate();
                $newsModel->approvedDt = Helper::getcurrentExpressionDate();
                $newsModel->status = 'E';
                $this->repository->add($newsModel);
                $this->flashmessenger()->addMessage("News Successfully added!!!");
                return $this->redirect()->toRoute("news");
            }
        }

        $newsType = [
            'NEWS' => 'NEWS',
            'NOTICE' => 'NOTICE',
            'CIRCULAR' => 'CIRCULAR',
            'RULE' => 'RULE',
            'OTHERS' => 'OTHERS'
        ];

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'newsTypeValue' => $newsType,
                    'company' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], "COMPANY_NAME", "ASC"),
                    'branch' => $departmentRepo->fetchAllBranchAndCompany(),
                    'designation' => $companyRepo->fetchAllDesignationAndCompany(),
                    'department' => $departmentRepo->fetchAllBranchAndDepartment()
        ]);
    }

    public function editAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('news');
        }
        $this->initializeForm();

        $request = $this->getRequest();
        $newsModel = new NewsModel();
        if (!$request->isPost()) {
            $newsModel->exchangeArrayFromDB($this->repository->fetchById($id));
            $this->form->bind($newsModel);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $newsModel->exchangeArrayFromForm($this->form->getData());
                $newsModel->modifiedBy = $this->employeeId;
                $newsModel->modifiedDt = Helper::getcurrentExpressionDate();
                $this->repository->edit($newsModel, $id);
                $this->flashmessenger()->addMessage("News Successfully Updated!!!");
                return $this->redirect()->toRoute("news");
            }
        }


        $companyRepo = new CompanyRepository($this->adapter);
        $departmentRepo = new DepartmentRepository($this->adapter);
        $newsType = [
            'NEWS' => 'NEWS',
            'NOTICE' => 'NOTICE',
            'CIRCULAR' => 'CIRCULAR',
            'RULE' => 'RULE',
            'OTHERS' => 'OTHERS'
        ];

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'newsTypeValue' => $newsType,
                    'company' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], "COMPANY_NAME", "ASC"),
                    'branch' => $departmentRepo->fetchAllBranchAndCompany(),
                    'designation' => $companyRepo->fetchAllDesignationAndCompany(),
                    'department' => $departmentRepo->fetchAllBranchAndDepartment()
        ]);
    }

    public function deleteAction() {
        $id = $this->params()->fromRoute('id');
        if ($id == 0) {
            $this->redirect()->toRoute('news');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("News Successfully Deleted!!!");
        return $this->redirect()->toRoute("news");
    }

}
