<?php

namespace Setup\Controller;

/**
 * Master Setup for Branch
 * Branch controller.
 * Created By: Ukesh Gaiju
 * Edited By: Somkala Pachhai
 * Date: August 3, 2016, Wednesday
 * Last Modified By: Somkala Pachhai
 * Last Modified Date: August 10,2016, Wednesday
 */
use Application\Helper\Helper;
use Setup\Form\BranchForm;
use Setup\Helper\EntityHelper;
use Setup\Model\Branch;
use Setup\Repository\BranchRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class BranchController extends AbstractActionController {

    private $form;
    private $repository;
    private $adapter;
    private $employeeId;

    function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new BranchRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $branchForm = new BranchForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($branchForm);
        }
    }

    public function indexAction() {
        $branchesRaw = $this->repository->fetchAll();
        $branches = [];

        foreach ($branchesRaw as $branch) {
            array_push($branches, $branch);
        }

        return Helper::addFlashMessagesToArray($this, ['branches' => $branches]);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $branch = new Branch();
                $branch->exchangeArrayFromForm($this->form->getData());
                $branch->branchId = ((int) Helper::getMaxId($this->adapter, "HRIS_BRANCHES", "BRANCH_ID")) + 1;
                $branch->createdDt = Helper::getcurrentExpressionDate();
                $branch->createdBy = $this->employeeId;
                $branch->status = 'E';

                $this->repository->add($branch);

                $this->flashmessenger()->addMessage("Branch Successfully Added!!!");
                return $this->redirect()->toRoute("branch");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'countries' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HRIS_COUNTRIES),
                    'customRenderer' => Helper::renderCustomView()
                        ]
        );
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $this->initializeForm();
        $request = $this->getRequest();

        $branch = new Branch();
        if (!$request->isPost()) {
            $branch->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($branch);
        } else {
            $modifiedDt = date('d-M-y');
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $branch->exchangeArrayFromForm($this->form->getData());
                $branch->modifiedDt = Helper::getcurrentExpressionDate();
                $branch->modifiedBy = $this->employeeId;
                $this->repository->edit($branch, $id);
                $this->flashmessenger()->addMessage("Branch Successfully Updated!!!");
                return $this->redirect()->toRoute("branch");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'id' => $id,
                    'countries' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HRIS_COUNTRIES),
                    'customRenderer' => Helper::renderCustomView()
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('branch');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Branch Successfully Deleted!!!");
        return $this->redirect()->toRoute('branch');
    }

}
