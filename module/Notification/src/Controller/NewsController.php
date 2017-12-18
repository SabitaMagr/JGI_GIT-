<?php

namespace Notification\Controller;

use Application\Helper\EntityHelper as ApplicationEntityHelper;
use Application\Helper\Helper;
use Exception;
use Notification\Form\NewsForm;
use Notification\Model\NewsEmployeeModel;
use Notification\Model\NewsFile;
use Notification\Model\NewsModel;
use Notification\Model\NewsTypeModel;
use Notification\Repository\NewsEmployee;
use Notification\Repository\NewsFileRepository;
use Notification\Repository\NewsRepository;
use Setup\Model\Company;
use Setup\Repository\DepartmentRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

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
        $list = Helper::extractDbData($result);
        return Helper::addFlashMessagesToArray($this, [
                    'news' => $list
        ]);
    }

    public function addAction() {
        $this->initializeForm();
//        $employeeRepo = new EmployeeRepository($this->adapter);
//        $employeeDetail = $employeeRepo->fetchById($this->employeeId);
        $request = $this->getRequest();

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
                ($newsModel->companyId == -1) ? $newsModel->companyId = NULL : NUll;
                ($newsModel->branchId == -1) ? $newsModel->branchId = NULL : NUll;
                ($newsModel->departmentId == -1) ? $newsModel->departmentId = NULL : NULL;
                ($newsModel->designationId == -1) ? $newsModel->designationId = NULL : NUll;

                if ($request->getPost()['employee']) {
                    $employees = $request->getPost()['employee'];
                    $newsEmpModel = new NewsEmployeeModel();
                    $newsEmployeeRepo = new NewsEmployee($this->adapter);
                    $newsEmpModel->newsId = $newsModel->newsId;
                    foreach ($employees as $emp) {
                        $newsEmpModel->employeeId = $emp;
                        $newsEmployeeRepo->add($newsEmpModel);
                    }
                }
                $this->repository->add($newsModel);
                $this->flashmessenger()->addMessage("News Successfully added!!!");
                return $this->redirect()->toRoute("news");
            }
        }


        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'newsTypeValue' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, NewsTypeModel::TABLE_NAME, NewsTypeModel::NEWS_TYPE_ID, [NewsTypeModel::NEWS_TYPE_DESC], ["STATUS" => "E"], "NEWS_TYPE_DESC", "ASC", null, true, true),
                    'searchValues' => ApplicationEntityHelper::getSearchData($this->adapter),
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

        $departmentRepo = new DepartmentRepository($this->adapter);

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'newsTypeValue' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, NewsTypeModel::TABLE_NAME, NewsTypeModel::NEWS_TYPE_ID, [NewsTypeModel::NEWS_TYPE_DESC], ["STATUS" => "E"], "NEWS_TYPE_DESC", "ASC", null, true, true),
                    'company' => ApplicationEntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], "COMPANY_NAME", "ASC", null, false, true),
                    'branch' => $departmentRepo->fetchAllBranchAndCompany(),
                    'designation' => $this->repository->fetchAllDesignationAndCompany(),
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

    public function allNewsTypeListAction() {
        $id = $this->params()->fromRoute('id');
         $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->allNewsTypeWise($this->employeeId,$id);
                $list = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $list, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'acl' => $this->acl
        ]);
    }

    public function fileUploadAction() {
        $request = $this->getRequest();
        $responseData = [];
        $files = $request->getFiles()->toArray();
        try {
            if (sizeof($files) > 0) {
                $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                $unique = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;
                $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/news/" . $newFileName);
                if ($success) {
                    $responseData = ["success" => true, "data" => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext]];
                }
            }
        } catch (Exception $e) {
            $responseData = [
                "success" => false,
                "message" => $e->getMessage(),
                "traceAsString" => $e->getTraceAsString(),
                "line" => $e->getLine()
            ];
        }
        return new JsonModel($responseData);
    }

    public function pushNewsFileAction() {
        try {
            $newsId = $this->params()->fromRoute('id');
            $request = $this->getRequest();
            $data = $request->getPost();

            $newsFile = new NewsFile();
            $return = [];
            if ($data['newsTypeId'] == null) {
                $newsFile->newsFileId = ((int) Helper::getMaxId($this->adapter, 'HRIS_NEWS_FILE', 'NEWS_FILE_ID')) + 1;
                $newsFile->newsId = $newsId;
                $newsFile->filePath = $data['filePath'];
                $newsFile->fileName = $data['fileName'];
                $newsFile->status = 'E';
                $newsFile->createdDt = Helper::getcurrentExpressionDate();
                $newsFile->createdBy = $this->employeeId;

                $newsFileRepo = new NewsFileRepository($this->adapter);
                $newsFileRepo->add($newsFile);

                $return = ["success" => true, "data" => ['newsFileId' => $newsFile->newsFileId]];
            } else {
                $newsFile->filePath = $data['filePath'];
                $newsFileRepo = new NewsFileRepository($this->adapter);
                $newsFileRepo->edit($newsFile, $data['newsTypeId']);
                $return = ["success" => true, "data" => ['newsFileId' => $data['newsTypeId']]];
            }

            return new JsonModel(['success' => true, 'data' => $return, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
