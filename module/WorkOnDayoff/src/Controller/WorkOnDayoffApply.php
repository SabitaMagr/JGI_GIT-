<?php
namespace WorkOnDayoff\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Application\Helper\EntityHelper;
use Zend\Form\Annotation\AnnotationBuilder;
use SelfService\Form\WorkOnDayoffForm;
use Setup\Model\HrEmployees;
use SelfService\Repository\WorkOnDayoffRepository;
use SelfService\Model\WorkOnDayoff;

class WorkOnDayoffApply extends AbstractActionController{
    private $form;
    private $adapter;
    private $workOnDayoffRepository;
    
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->workOnDayoffRepository = new WorkOnDayoffRepository($adapter);
    }
    public function initializeForm(){
        $builder = new AnnotationBuilder();
        $form = new WorkOnDayoffForm();
        $this->form = $builder->createForm($form);
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new WorkOnDayoff();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->id = ((int) Helper::getMaxId($this->adapter, WorkOnDayoff::TABLE_NAME, WorkOnDayoff::ID)) + 1;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $model->deductOnSalary = 'Y';
                $this->workOnDayoffRepository->add($model);
                $this->flashmessenger()->addMessage("Work on Day-off Request Successfully added!!!");
                return $this->redirect()->toRoute("status");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employees'=> EntityHelper::getTableKVListWithSortOption($this->adapter, "HR_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"],["STATUS"=>'E','RETIRED_FLAG'=>'N'],"FIRST_NAME","ASC"," "),
        ]);
    }
}