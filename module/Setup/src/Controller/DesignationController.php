<?php

namespace Setup\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Setup\Form\DesignationForm;
use Setup\Model\Company;
use Setup\Model\Designation;
use Setup\Repository\DesignationRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DesignationController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;

    function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new DesignationRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $designationForm = new DesignationForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($designationForm);
        }
    }

    public function indexAction() {
        $designations = $this->repository->fetchAll();

        $designationList = [];

        foreach ($designations as $designationRow) {
            array_push($designationList, $designationRow);
        }

        return Helper::addFlashMessagesToArray($this, ["designations" => $designationList]);
    }

    public function addAction() {

        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $designation = new Designation();
                $designation->exchangeArrayFromForm($this->form->getData());
                $designation->createdDt = Helper::getcurrentExpressionDate();
                $designation->createdBy = $this->employeeId;
                $designation->designationId = ((int) Helper::getMaxId($this->adapter, "HRIS_DESIGNATIONS", "DESIGNATION_ID")) + 1;
                $designation->status = 'E';
                $this->repository->add($designation);

                $this->flashmessenger()->addMessage("Designation Successfully added!!!");
                return $this->redirect()->toRoute("designation");
            }
        }
        $designationList = EntityHelper::getTableKVListWithSortOption($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], ["STATUS" => "E"], "DESIGNATION_TITLE", "ASC");
        $designationList1 = ["" => "none"] + $designationList;
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'customRender' => Helper::renderCustomView(),
                    'designationList' => $designationList1,
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC"),
                    'messages' => $this->flashmessenger()->getMessages()
                        ]
                )
        );
    }

    public function editAction() {

        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('designation');
        }
        $this->initializeForm();
        $request = $this->getRequest();
        $designation = new Designation();
        if (!$request->isPost()) {
            $designation->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($designation);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {

                $designation->exchangeArrayFromForm($this->form->getData());
                $designation->modifiedDt = Helper::getcurrentExpressionDate();
                $designation->modifiedBy = $this->employeeId;
                $this->repository->edit($designation, $id);

                $this->flashmessenger()->addMessage("Designation Successfully Updated!!!");
                return $this->redirect()->toRoute("designation");
            }
        }
        $designationList = EntityHelper::getTableKVList($this->adapter, Designation::TABLE_NAME, Designation::DESIGNATION_ID, [Designation::DESIGNATION_TITLE], ["STATUS" => "E"]);
        $designationList1 = ["" => "none"] + $designationList;
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'customRender' => Helper::renderCustomView(),
                    'designationList' => $designationList1,
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC"),
                    'messages' => $this->flashmessenger()->getMessages(),
                    'id' => $id
                ])
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('designation');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Designation Successfully Deleted!!!");
        return $this->redirect()->toRoute('designation');
    }

}

/* End of file DesignationController.php */
/* Location: ./Setup/src/Controller/DesignationController.php */
