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
use Setup\Helper\EntityHelper;

class DepartmentController extends AbstractActionController{
	private $entityManager;
    private $hrDepartments;
    private $departmentForm;
    private $hydrator;
// 
	function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
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

        $this->hrDepartments->setDepartmentId(19);
        $this->hrDepartments->setDepartmentCode("BC002");
        $this->hrDepartments->setDepartmentName("Hr");
        $this->hrDepartments->setRemarks("hello");
        $this->hrDepartments->setStatus("E");
        $this->entityManager->persist($this->hrDepartments);
        $this->entityManager->flush();

        $request = $this->getRequest(); 
        if ($request->isPost()) {
           
            $this->departmentForm->setData($request->getPost());
            //print_r($request->getPost());  die();

            if ($this->departmentForm->isValid()){
                $formData = $this->departmentForm->getData();

                $this->hrDepartments = EntityHelper::hydrate($this->entityManager,HrDepartments::class,$formData);
                
                //print_r($this->hrDepartments); die();

                $em =$this->entityManager;
                $em->getConnection()->beginTransaction(); // suspend auto-commit      
                
                try {
                  $em->persist($this->hrDepartments);
                  $em->flush();
                  $em->getConnection()->commit();
                } catch (Exception $e) {
                  $em->getConnection()->rollback();
                  throw $e;
                }  

                $this->flashmessenger()->addMessage("Department Successfully added!!!");
                return $this->redirect()->toRoute("department");
            }
        } 
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->departmentForm,
                'departments'=> EntityHelper::getDepartmentKVList($this->entityManager),
                'messages' => $this->flashmessenger()->getMessages()
             ]
            )
        );               
	}

	public function editAction(){
		
		$id=(int) $this->params()->fromRoute("id");
		if($id===0){
			return $this->redirect()->toRoute('department');
		}
        $this->initializeForm();

        $request=$this->getRequest();

        $departmentRecord = $this->entityManager->find(HrDepartments::class,$id);
        $departmentRecord1 = EntityHelper::extract($this->entityManager,$departmentRecord);

        if(!$request->isPost()){
            $this->departmentForm->bind((object)$departmentRecord1);
        }else{

            $this->departmentForm->setData($request->getPost());

            //print_r($request->getPost());die();
            
            //$modifiedDt = date('d-M-y H:i:s');
            
            if ($this->departmentForm->isValid()) {
                $formData = $this->departmentForm->getData();
                // $newFormData = array_merge($formData,['modifiedDt'=>$modifiedDt]);
                //print"<pre>";print_r($newFormData);exit;
                $this->hrDepartments = EntityHelper::hydrate($this->entityManager,HrDepartments::class, $formData);
// print"<pre>";print_r($this->hrDepartments);exit;
               // $date = new \DateTime($modifiedDt);

                //$this->hrDepartments->setModifiedDt($date);
                //$this->hrDepartments->setDepartmentId($id);
                //$this->hrDepartments->setModifiedDt($modifiedDt);

                // print_r($this->hrDepartments);die();
                // $this->entityManager->getConnection()->beginTransaction();
                // $this->entityManager->merge($this->hrDepartments);
                // $this->entityManager->flush();
                // $this->entityManager->getConnection()->commit();

                $em =$this->entityManager;
                $em->getConnection()->beginTransaction(); // suspend auto-commit
                try {
                  $em->persist($this->hrDepartments);
                  $em->flush();
                  $em->getConnection()->commit();
                } catch (Exception $e) {
                  $em->getConnection()->rollback();
                  throw $e;
                }
                $this->flashmessenger()->addMessage("Department Successfully Updated!!!");
                return $this->redirect()->toRoute("department");            
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,['form'=>$this->departmentForm,'id'=>$id,
            'departments'=> EntityHelper::getDepartmentKVList($this->entityManager,$id)
            ]
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