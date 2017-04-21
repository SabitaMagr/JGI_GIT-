<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/16
 * Time: 4:41 PM
 */
namespace Setup\Controller;

use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Setup\Model\AcademicProgram;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Authentication\AuthenticationService;
use Setup\Model\AcademicCourse;
use Setup\Form\AcademicCourseForm;
use Setup\Repository\AcademicCourseRepository;
use Zend\View\Model\ViewModel;

class AcademicCourseController extends AbstractActionController {
    private $repository;
    private $form;
    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->repository = new AcademicCourseRepository($adapter);
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm()
    {
        $form = new AcademicCourseForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($form);
        }
    }

    public function indexAction()
    {
        $courseList = $this->repository->fetchAll();
        $academicCourses = [];
        foreach($courseList as $courseRow){
            array_push($academicCourses, $courseRow);
        }
        return Helper::addFlashMessagesToArray($this, ['academicCourses' => $academicCourses]);
    }

    public function addAction()
    {                    
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $academicCourse = new AcademicCourse();
                $academicCourse->exchangeArrayFromForm($this->form->getData());
                $academicCourse->academicCourseId=((int) Helper::getMaxId($this->adapter,AcademicCourse::TABLE_NAME,AcademicCourse::ACADEMIC_COURSE_ID))+1;
                $academicCourse->createdDt = Helper::getcurrentExpressionDate();
                $academicCourse->createdBy = $this->employeeId;
                $academicCourse->status ='E';
                $this->repository->add($academicCourse);

                $this->flashmessenger()->addMessage("Academic Course Successfully added!!!");
                return $this->redirect()->toRoute("academicCourse");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'messages' => $this->flashmessenger()->getMessages(),
                'programs' => EntityHelper::getTableKVListWithSortOption($this->adapter, AcademicProgram::TABLE_NAME, AcademicProgram::ACADEMIC_PROGRAM_ID, ["ACADEMIC_PROGRAM_NAME"], ["STATUS"=>'E'], "ACADEMIC_PROGRAM_NAME", "ASC",null,false,true)
            ]
        )
        );
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('academicCourse');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $academicCourse = new AcademicCourse();
        if (!$request->isPost()) {
            $academicCourse->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($academicCourse);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $academicCourse->exchangeArrayFromForm($this->form->getData());
                $academicCourse->modifiedDt = Helper::getcurrentExpressionDate();
                $academicCourse->modifiedBy = $this->employeeId;
                $this->repository->edit($academicCourse, $id);
                $this->flashmessenger()->addMessage("Academic Course Successfully Updated!!!");
                return $this->redirect()->toRoute("academicCourse");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this, [
                'form' => $this->form,
                'id' => $id,
                'programs' => EntityHelper::getTableKVListWithSortOption($this->adapter, AcademicProgram::TABLE_NAME, AcademicProgram::ACADEMIC_PROGRAM_ID, ["ACADEMIC_PROGRAM_NAME"], ["STATUS"=>'E'], "ACADEMIC_PROGRAM_NAME", "ASC",null,false,true)
            ]
        );
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('academicCourse');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Academic Course Successfully Deleted!!!");
        return $this->redirect()->toRoute('academicCourse');
    }
}