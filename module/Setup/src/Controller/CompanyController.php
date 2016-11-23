<?php

namespace Setup\Controller;

use Application\Helper\Helper;
use Setup\Model\Company;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Form\CompanyForm;
use Setup\Repository\CompanyRepository;

class CompanyController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;

    function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new CompanyRepository($adapter);
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
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $company = new Company();
                $company->exchangeArrayFromForm($this->form->getData());
                $company->createdDt = Helper::getcurrentExpressionDate();
                $company->companyId = ((int) Helper::getMaxId($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID)) + 1;
                $company->status = 'E';
                $this->repository->add($company);
                $this->flashmessenger()->addMessage("Company Successfully added!!!");
                return $this->redirect()->toRoute("company");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages()
                        ]
                )
        );
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
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $company->exchangeArrayFromForm($this->form->getData());
                $company->modifiedDt = Helper::getcurrentExpressionDate();
                unset($company->createdDt);
                unset($company->companyId);
                unset($company->status);
                $this->repository->edit($company, $id);
                $this->flashmessenger()->addMessage("Company Successfully Updated!!!");
                return $this->redirect()->toRoute("company");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, ['form' => $this->form, 'id' => $id]
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Company Successfully Deleted!!!");
        return $this->redirect()->toRoute('company');
    }

}

?>