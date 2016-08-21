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
use Setup\Helper\EntityHelper;
use Setup\Model\DepartmentRepository;
use Zend\Db\Adapter\AdapterInterface;


class DepartmentController extends AbstractActionController{
	
    private $department;
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
        $this->department = new DepartmentForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($this->department);
        }
    }

	public function indexAction(){
		$departmentList  = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this,['departments' => $departmentList]);
	}

	public function addAction(){
		
		$this->initializeForm();
        $request = $this->getRequest(); 

        if ($request->isPost()) {
           
            $this->form->setData($request->getPost());
          
            if ($this->form->isValid()){
                $this->department->exchangeArrayFromForm($this->form->getData());
                $this->repository->add($this->department);  
                $this->flashmessenger()->addMessage("Department Successfully added!!!");
                return $this->redirect()->toRoute("department");
            }
        } 
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'departments'=> EntityHelper::getDepartmentKVList($this->adapter),
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

        $modifiedDt = date("d-M-y");
        if(!$request->isPost()){
            
            $this->department->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind((object)$this->department->getArrayCopyForForm());
        }else{

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $this->department->exchangeArrayFromForm($this->form->getData());
                $this->repository->edit($this->department,$id,$modifiedDt);
                $this->flashmessenger()->addMessage("Department Successfully Updated!!!");
                return $this->redirect()->toRoute("department");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,['form'=>$this->form,'id'=>$id,
            //'departments'=> EntityHelper::getDepartmentKVList($this->entityManager,$id)
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