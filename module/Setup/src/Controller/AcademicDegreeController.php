<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/16
 * Time: 4:35 PM
 */
namespace Setup\Controller;

use Application\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Setup\Form\AcademicDegreeForm;
use Setup\Model\AcademicDegree;
use Setup\Repository\AcademicDegreeRepository;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;

class AcademicDegreeController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new AcademicDegreeRepository($adapter);
    }

    public function initializeForm()
    {
        $form = new AcademicDegreeForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($form);
        }
    }

    public function indexAction()
    {
        $degreeList = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['degrees' => $degreeList]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $academicDegree = new AcademicDegree();
                $academicDegree->exchangeArrayFromForm($this->form->getData());
                $academicDegree->academicDegreeId=((int) Helper::getMaxId($this->adapter,AcademicDegree::TABLE_NAME,AcademicDegree::ACADEMIC_DEGREE_ID))+1;
                $academicDegree->createdDt = Helper::getcurrentExpressionDate();
                $academicDegree->status ='E';
                $this->repository->add($academicDegree);

                $this->flashmessenger()->addMessage("Academic Degree Successfully added!!!");
                return $this->redirect()->toRoute("academicDegree");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'messages' => $this->flashmessenger()->getMessages()
            ]
        )
        );
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('academicDegree');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $academicDegree = new AcademicDegree();
        if (!$request->isPost()) {
            $academicDegree->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($academicDegree);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $academicDegree->exchangeArrayFromForm($this->form->getData());
                $academicDegree->modifiedDt = Helper::getcurrentExpressionDate();

                $this->repository->edit($academicDegree, $id);
                $this->flashmessenger()->addMessage("Academic Degree Successfully Updated!!!");
                return $this->redirect()->toRoute("academicDegree");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this, ['form' => $this->form, 'id' => $id]
        );
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('academicDegree');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Academic Degree Successfully Deleted!!!");
        return $this->redirect()->toRoute('academicDegree');
    }
}

