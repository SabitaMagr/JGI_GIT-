<?php

namespace Setup\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\CompanyForm;
use Setup\Model\Company;
use Setup\Model\EmployeeFile as EmployeeFile2;
use Setup\Repository\CompanyRepository;
use Setup\Repository\EmployeeFile;
use System\Repository\SynergyRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class FileTypeController extends HrisController {

    private $synergyRepo;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(CompanyRepository::class);
        $this->initializeForm(CompanyForm::class);
        $this->synergyRepo = new SynergyRepository($adapter);
    }

    public function indexAction() {
        echo 'sdfds';
        die();
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
                $companyList = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $companyList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function addAction() {
        echo 'add';
        DIE();
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
                $this->flashmessenger()->addMessage("Company Successfully added.");
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


    public function editAction() {
        echo 'edit';
        DIE();
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('company');
        }
        $request = $this->getRequest();

        $company = new Company();
        if (!$request->isPost()) {
            $company->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->prepareForm($company->companyCode);
            $this->form->bind($company);
        } else {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            if ($this->form->isValid()) {
                $company->exchangeArrayFromForm($this->form->getData());
                $company->modifiedDt = Helper::getcurrentExpressionDate();
                $company->modifiedBy = $this->employeeId;
                $company->logo = $postedData['logo'];
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
                    'imageData' => $imageData,
                    'customRenderer' => Helper::renderCustomView(),
                        ]
        );
    }


    public function deleteAction() {
        echo 'DELETE';
        DIE();
//        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
//            return;
//        };
//        $id = (int) $this->params()->fromRoute("id");
//        $this->repository->delete($id);
//        $this->flashmessenger()->addMessage("Company Successfully Deleted!!!");
//        return $this->redirect()->toRoute('company');
    }

}

?>