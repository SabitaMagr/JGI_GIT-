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
use Setup\Model\Branch;
use Setup\Repository\BranchRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class BranchController extends AbstractActionController
{
    private $form;
    private $repository;

    function __construct(AdapterInterface $adapter)
    {
        $this->repository = new BranchRepository($adapter);
    }


    public function initializeForm()
    {
        $branchForm = new BranchForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($branchForm);
        }
    }

    public function indexAction()
    {
        $branches = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['branches' => $branches]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $branch=new Branch();
                $branch->exchangeArrayFromForm($this->form->getData());
                $branch->createdDt=date('d-M-y');
                $this->repository->add($branch);

                $this->flashmessenger()->addMessage("Branch Successfully Added!!!");
                return $this->redirect()->toRoute("branch");
            }
        }
        return Helper::addFlashMessagesToArray($this, ['form' => $this->form]);
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        $this->initializeForm();
        $request = $this->getRequest();

            $branch=new Branch();
        if (!$request->isPost()) {
            $branch->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($branch);
        } else {
            $modifiedDt = date('d-M-y');
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $branch->exchangeArrayFromForm($this->form->getData());
//                $branch->modifiedDt=$modifiedDt;
                $branch->modifiedDt="to_date('2014-01-01', 'YYYY-MM-DD')";
                $this->repository->edit($branch, $id);
                $this->flashmessenger()->addMessage("Branch Successfully Updated!!!");
                return $this->redirect()->toRoute("branch");
            }
        }
        return Helper::addFlashMessagesToArray($this, ['form' => $this->form, 'id' => $id]);
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('branch');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Branch Successfully Deleted!!!");
        return $this->redirect()->toRoute('branch');
    }

}


/* End of file BranchController.php */
/* Location: ./Setup/src/Controller/BranchController.php */