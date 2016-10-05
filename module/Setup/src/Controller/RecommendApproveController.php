<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 10/3/16
 * Time: 12:37 PM
 */
namespace Setup\Controller;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Setup\Model\RecommendApprove;
use Setup\Form\RecommendApproveForm;
use Zend\Form\Annotation\AnnotationBuilder;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Setup\Repository\RecommendApproveRepository;

class RecommendApproveController extends AbstractActionController {
    private $form;
    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new RecommendApproveRepository($adapter);
    }
    public function indexAction()
    {
        $list = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['list' => $list]);
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $recommendApproveForm = new RecommendApproveForm();
        $this->form = $builder->createForm($recommendApproveForm);
    }
    public function addAction(){
        $request = $this->getRequest();
        $this->initializeForm();
        if($request->isPost()){
            $this->form->setData($request->getPost());
            if($this->form->isValid()){
                $recommendApprove = new RecommendApprove();
                $recommendApprove->exchangeArrayFromForm($this->form->getData());
                $recommendApprove->createdDt = Helper::getcurrentExpressionDate();
                $recommendApprove->status='E';

                $this->repository->add($recommendApprove);

                $this->flashmessenger()->addMessage("Recommender And Approver Successfully Assigned!!!");
                return $this->redirect()->toRoute("recommendapprove");
            }
        }

        return Helper::addFlashMessagesToArray($this,
            [
                'form' => $this->form,
                'employees' => $this->repository->getEmployees()
            ]
        );

    }
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        $this->initializeForm();
        $request = $this->getRequest();

        $recommendApprove = new RecommendApprove();
        if (!$request->isPost()) {
            $recommendApprove->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($recommendApprove);
        } else {
            $modifiedDt = date('d-M-y');
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $recommendApprove->exchangeArrayFromForm($this->form->getData());
                $recommendApprove->modifiedDt = Helper::getcurrentExpressionDate();
                $this->repository->edit($recommendApprove, $id);

                $this->flashmessenger()->addMessage("Recommender And Approver Successfully Assigned!!!");
                return $this->redirect()->toRoute("recommendapprove");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            //EntityHelper::getTableKVList($this->adapter,"HR_EMPLOYEES","EMPLOYEE_ID",["FIRST_NAME","MIDDLE_NAME","LAST_NAME"],["STATUS"=>"E"])
            'employees' => $this->repository->getEmployees($id)
        ]);
    }
}
