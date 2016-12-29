<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/16
 * Time: 4:37 PM
 */
namespace Setup\Controller;

use Application\Helper\Helper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Authentication\AuthenticationService;
use Setup\Form\AcademicUniversityForm;
use Setup\Repository\AcademicUniversityRepository;
use Setup\Model\AcademicUniversity;
use Zend\View\Model\ViewModel;

class AcademicUniversityController extends AbstractActionController {
    private $repository;
    private $form;
    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->repository = new AcademicUniversityRepository($adapter);
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm()
    {
        $form = new AcademicUniversityForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($form);
        }
    }

    public function indexAction()
    {
        $universityList = $this->repository->fetchAll();
        $academicUniversities = [];
        foreach($universityList  as $universityRow){
            array_push($academicUniversities, $universityRow);
        }
        return Helper::addFlashMessagesToArray($this, ['academicUniversities' => $academicUniversities]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $academicUniversity = new AcademicUniversity();
                $academicUniversity->exchangeArrayFromForm($this->form->getData());
                $academicUniversity->academicUniversityId=((int) Helper::getMaxId($this->adapter,AcademicUniversity::TABLE_NAME,AcademicUniversity::ACADEMIC_UNIVERSITY_ID))+1;
                $academicUniversity->createdDt = Helper::getcurrentExpressionDate();
                $academicUniversity->createdBy = $this->employeeId;
                $academicUniversity->status ='E';
                $this->repository->add($academicUniversity);

                $this->flashmessenger()->addMessage("Academic University Successfully added!!!");
                return $this->redirect()->toRoute("academicUniversity");
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
            return $this->redirect()->toRoute('academicUniversity');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $academicUniversity = new AcademicUniversity();
        if (!$request->isPost()) {
            $academicUniversity->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($academicUniversity);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $academicUniversity->exchangeArrayFromForm($this->form->getData());
                $academicUniversity->modifiedDt = Helper::getcurrentExpressionDate();
                $academicUniversity->modifiedBy = $this->employeeId;
                $this->repository->edit($academicUniversity, $id);
                $this->flashmessenger()->addMessage("Academic University Successfully Updated!!!");
                return $this->redirect()->toRoute("academicUniversity");
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
            return $this->redirect()->toRoute('academicUniversity');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Academic University Successfully Deleted!!!");
        return $this->redirect()->toRoute('academicUniversity');
    }
}