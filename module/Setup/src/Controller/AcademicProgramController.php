<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/16
 * Time: 4:40 PM
 */
namespace Setup\Controller;

use Application\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Model\AcademicProgram;
use Setup\Form\AcademicProgramForm;
use Setup\Repository\AcademicProgramRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;

class AcademicProgramController extends AbstractActionController {
    private $repository;
    private $form;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new AcademicProgramRepository($adapter);
    }

    public function initializeForm()
    {
        $form = new AcademicProgramForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($form);
        }
    }

    public function indexAction()
    {
        $programList = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['programs' => $programList]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $academicProgram = new AcademicProgram();
                $academicProgram->exchangeArrayFromForm($this->form->getData());
                $academicProgram->academicProgramId=((int) Helper::getMaxId($this->adapter,AcademicProgram::TABLE_NAME,AcademicProgram::ACADEMIC_PROGRAM_ID))+1;
                $academicProgram->createdDt = Helper::getcurrentExpressionDate();
                $academicProgram->status ='E';
                $this->repository->add($academicProgram);

                $this->flashmessenger()->addMessage("Academic Program Successfully added!!!");
                return $this->redirect()->toRoute("academicProgram");
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
            return $this->redirect()->toRoute('academicProgram');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $academicProgram = new AcademicProgram();
        if (!$request->isPost()) {
            $academicProgram->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($academicProgram);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $academicProgram->exchangeArrayFromForm($this->form->getData());
                $academicProgram->modifiedDt = Helper::getcurrentExpressionDate();
                $this->repository->edit($academicProgram, $id);
                $this->flashmessenger()->addMessage("Academic Program Successfully Updated!!!");
                return $this->redirect()->toRoute("academicProgram");
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
            return $this->redirect()->toRoute('academicProgram');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Academic Program Successfully Deleted!!!");
        return $this->redirect()->toRoute('academicProgram');
    }
}