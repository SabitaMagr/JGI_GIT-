<?php

namespace Setup\Controller;

/**
 * Master Setup for Department
 * Department controller.
 * Created By: Somkala Pachhai
 * Edited By: Somkala Pachhai
 * Date: August 5, 2016, Friday
 * Last Modified By: Somkala Pachhai
 * Last Modified Date: August 10,2016, Wednesday
 */
use Application\Helper\Helper;
use Setup\Form\DepartmentForm;
use Setup\Helper\EntityHelper;
use Setup\Model\Department;
use Setup\Repository\DepartmentRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class DepartmentController extends AbstractActionController
{

    private $form;
    private $repository;
    private $adapter;

    function __construct(AdapterInterface $adapter)
    {
        $this->repository = new DepartmentRepository($adapter);
        $this->adapter = $adapter;
    }

    public function initializeForm()
    {
        $departmentForm = new DepartmentForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($departmentForm);
        }
    }

    public function indexAction()
    {
        $departmentList = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['departments' => $departmentList]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $department = new Department();
                $department->exchangeArrayFromForm($this->form->getData());
                $department->createdDt = date('d-M-y');
                $this->repository->add($department);
                $this->flashmessenger()->addMessage("Department Successfully added!!!");
                return $this->redirect()->toRoute("department");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'departments' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_DEPARTMENTS),
                'messages' => $this->flashmessenger()->getMessages()
            ]
        )
        );
    }

    public function editAction()
    {

        $id = (int)$this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('department');
        }
        $this->initializeForm();
        $request = $this->getRequest();

        $department = new Department();
        if (!$request->isPost()) {
            $department->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($department);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $department->exchangeArrayFromForm($this->form->getData());
                $department->modifiedDt = date("d-M-y");
                $this->repository->edit($department, $id);
                $this->flashmessenger()->addMessage("Department Successfully Updated!!!");
                return $this->redirect()->toRoute("department");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this, ['form' => $this->form, 'id' => $id,
                'departments' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_DEPARTMENTS)
            ]
        );
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Department Successfully Deleted!!!");
        return $this->redirect()->toRoute('department');
    }
}

/* End of file DepartmentController.php */
/* Location: ./Setup/src/Controller/DepartmentController.php */
?>