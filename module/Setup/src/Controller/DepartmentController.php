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
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Form\DepartmentForm;

use Setup\Entity\HrDepartments;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class DepartmentController extends AbstractActionController{
	private $entityManager;
    private $hrDepartments;
    private $departmentForm;
    private $hydrator;

	function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
        $this->hydrator = new DoctrineHydrator($entityManager);
        $this->hrDepartments = new HrDepartments();
	}

	 public function initializeForm()
    {
        $form = new DepartmentForm();
        $builder = new AnnotationBuilder();
        if (!$this->departmentForm) {
            $this->departmentForm = $builder->createForm($form);
        }
    }


	public function indexAction(){
		$departments = $this->entityManager->getRepository(HrDepartments::class)->findAll();
        return Helper::addFlashMessagesToArray($this,['departments' => $departments]);
	}

	public function addAction(){
		
		$this->initializeForm();

        $request = $this->getRequest();
        if (!$request->isPost()) {
           return new ViewModel(Helper::addFlashMessagesToArray(
                $this,
                [
                    'form' => $this->departmentForm,
                    'messages' => $this->flashmessenger()->getMessages()
                 ]
                )
            );
        }
        $this->departmentForm->setData($request->getPost());

        if ($this->departmentForm->isValid()) {
            $formData = $this->departmentForm->getData();
            $this->hrDepartments = $this->hydrator->hydrate($formData,$this->hrDepartments);

            $this->entityManager->persist($this->hrDepartments);
            $this->entityManager->flush();
            
            $this->flashmessenger()->addMessage("Department Successfully added!!!");
            return $this->redirect()->toRoute("department");
        } else {
            return new ViewModel(Helper::addFlashMessagesToArray(
                $this,
                [
                    'form' => $this->departmentForm,
                    'messages' => $this->flashmessenger()->getMessages()
                 ]
                )
            );
        }        
	}

	public function editAction(){
		
		$id=(int) $this->params()->fromRoute("id");
		if($id===0){
			return $this->redirect()->toRoute('department');
		}
        $this->initializeForm();

        $request=$this->getRequest();

        if(!$request->isPost()){
            $departmentRecord = (object)$this->entityManager->find(HrDepartments::class,$id)->getArrayCopy();
            $this->departmentForm->bind($departmentRecord);
        }else{

            $this->departmentForm->setData($request->getPost());
            $modifiedDt = date('Y-m-d');
            if ($this->departmentForm->isValid()) {
                $formData = $this->departmentForm->getData();
                $newFormData = array_merge($formData,['modifiedDt'=>$modifiedDt]);
                $this->hrDepartments = $this->hydrator->hydrate($newFormData,$this->hrDepartments);
                $this->hrDepartments->setDepartmentId($id);

                $this->entityManager->merge($this->hrDepartments);
                $this->entityManager->flush();

                $this->flashmessenger()->addMessage("Department Successfully Updated!!!");
                return $this->redirect()->toRoute("department");
            }
        }
        return Helper::addFlashMessagesToArray(
                    $this,['form'=>$this->departmentForm,'id'=>$id]
                 );
	}

	public function deleteAction(){
		$id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->hrDepartments =  $this->entityManager->find(HrDepartments::class, $id);
        $this->entityManager->remove($this->hrDepartments);
        $this->entityManager->flush();
        $this->flashmessenger()->addMessage("Department Successfully Deleted!!!");
        return $this->redirect()->toRoute('department');
    }


}
	

/* End of file DepartmentController.php */
/* Location: ./Setup/src/Controller/DepartmentController.php */

?>