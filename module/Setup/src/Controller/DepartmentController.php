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
use Setup\Entity\HrZones;
use Setup\Entity\HrPositions;

class DepartmentController extends AbstractActionController{
	private $entityManager;
    private $hrDepartments;
    private $departmentForm;
    private $hydrator;
 
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

        $hrPositions = new HrPositions();
        $hrPositions->setPositionId(3);
        //$hrPositions->setPositionCode("PC001");
        $hrPositions->setPositionName("Developer");
        $hrPositions->setStatus("E");
        $hrPositions->setRemarks("Heloo");

        // $hrZone = new HrZones();
        // $hrZone->setZoneId("2yui");
        // $hrZone->setZoneCode("ZOne1");
        // $hrZone->setZoneName("first zone");
        // $hrZone->setStatus("E");
        // $hrZone->setRemarks("sdfdsfds");

        print"<pre>";print_r($hrPositions); die();
      
        $this->hrDepartments->setDepartmentCode("BC002");
        $this->hrDepartments->setDepartmentName("Hr");
        $this->hrDepartments->setRemarks("hello");
        $this->hrDepartments->setStatus("E");
        $this->hrDepartments->setDepartmentId(19);
        $this->hrDepartments->setParentDepartment(1);
        
        $this->entityManager->persist($this->hrDepartments);

        $this->entityManager->flush();

        $request = $this->getRequest(); 
        if ($request->isPost()) {
           
            $this->departmentForm->setData($request->getPost());
          
            if ($this->departmentForm->isValid()){
                $formData = $this->departmentForm->getData();

                $this->hrDepartments = EntityHelper::hydrate($this->entityManager,HrDepartments::class,$formData);

                $em = $this->entityManager;
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

        $request = $this->getRequest();

        $departmentRecord = $this->entityManager->find(HrDepartments::class,$id);
        $departmentRecord1 = EntityHelper::extract($this->entityManager,$departmentRecord);
        $modifiedDt = date('Y-m-d');
        if(!$request->isPost()){
            $this->departmentForm->bind((object)$departmentRecord1);
        }else{

            $this->departmentForm->setData($request->getPost());
            
            if ($this->departmentForm->isValid()) {
                $formData = $this->departmentForm->getData();
                $newFormData = array_merge($formData,['modifiedDt'=>$modifiedDt]);
                $this->hrDepartments = EntityHelper::hydrate($this->entityManager,HrDepartments::class, $newFormData);
                $this->hrDepartments->setDepartmentId($id);

                $em =$this->entityManager;
                $em->getConnection()->beginTransaction(); // suspend auto-commit
                try {
                  $em->merge($this->hrDepartments);
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