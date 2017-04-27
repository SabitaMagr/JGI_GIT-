<?php

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\Helper;
use Exception;
use Setup\Form\CompanyForm;
use Setup\Model\Company;
use Setup\Model\EmployeeFile as EmployeeFile2;
use Setup\Repository\CompanyRepository;
use Setup\Repository\EmployeeFile;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CompanyController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;

    function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new CompanyRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $companyForm = new CompanyForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($companyForm);
        }
    }

    public function indexAction() {
        $companyList = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['companyList' => Helper::extractDbData($companyList)]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        $imageData = null;
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            if ($this->form->isValid()) {
                $company = new Company();
                $company->exchangeArrayFromForm($this->form->getData());
                $company->createdDt = Helper::getcurrentExpressionDate();
                $company->createdBy = $this->employeeId;
                $company->companyId = ((int) Helper::getMaxId($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID)) + 1;
                $company->logo = $postedData['logo'];
                $company->status = 'E';
                $this->repository->add($company);
                $this->flashmessenger()->addMessage("Company Successfully added!!!");
                return $this->redirect()->toRoute("company");
            } else {
                $imageData = $this->getFileInfo($this->adapter, $postedData['logo']);
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages(),
                    'imageData' => $imageData
                        ]
                )
        );
    }

    private function getFileInfo(AdapterInterface $adapter, $fileId) {
        $fileRepo = new EmployeeFile($adapter);
        $fileDetail = $fileRepo->fetchById($fileId);

        if ($fileDetail == null) {
            $imageData = [
                'fileCode' => null,
                'fileName' => null,
                'oldFileName' => null
            ];
        } else {
            $imageData = [
                'fileCode' => $fileDetail['FILE_CODE'],
                'oldFileName' => $fileDetail['FILE_PATH'],
                'fileName' => $fileDetail['FILE_NAME']
            ];
        }
        return $imageData;
    }

    public function editAction() {

        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('company');
        }
        $this->initializeForm();
        $request = $this->getRequest();

        $company = new Company();
        if (!$request->isPost()) {
            $company->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($company);
        } else {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            $company->logo = $postedData['logo'];
            if ($this->form->isValid()) {
                $company->exchangeArrayFromForm($this->form->getData());
                $company->modifiedDt = Helper::getcurrentExpressionDate();
                $company->modifiedBy = $this->employeeId;
                $this->repository->edit($company, $id);
                $this->flashmessenger()->addMessage("Company Successfully Updated!!!");
                return $this->redirect()->toRoute("company");
            }
        }

        $imageData = $this->getFileInfo($this->adapter, $company->logo);

        return Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'id' => $id,
                    'imageData' => $imageData
                        ]
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Company Successfully Deleted!!!");
        return $this->redirect()->toRoute('company');
    }

    public function fileUploadAction() {
        try {
            $request = $this->getRequest();
            $files = $request->getFiles()->toArray();
            if (sizeof($files) > 0) {
                $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                $unique = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;
                $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/" . $newFileName);
                if ($success) {
                    $fileRepository = new EmployeeFile($this->adapter);
                    $file = new EmployeeFile2();
                    $file->fileCode = ((int) Helper::getMaxId($this->adapter, 'HRIS_EMPLOYEE_FILE', 'FILE_CODE')) + 1;
                    $file->filePath = $fileName . "." . $ext;
                    $file->fileName = $newFileName;
                    $file->status = 'E';
                    $file->createdDt = Helper::getcurrentExpressionDate();
                    $fileRepository->add($file);
                    return new CustomViewModel(['success' => true, 'data' => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext, 'fileCode' => $file->fileCode], 'error' => '']);
                } else {
                    throw new Exception("Moving uploaded file failed");
                }
            } else {
                throw new Exception("No file is uploaded");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

}

?>