<?php

namespace HolidayManagement\Controller;

use Application\Helper\Helper;
use HolidayManagement\Form\HolidayForm;
use HolidayManagement\Model\Holiday;
use HolidayManagement\Repository\HolidayRepository;
use Setup\Helper\EntityHelper;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class HolidaySetup extends AbstractActionController
{
    private $repository;
    private $form;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->repository=new HolidayRepository($adapter);
        $this->adapter = $adapter;
    }
    public function initializeForm()
    {
        $leaveApplyForm = new HolidayForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($leaveApplyForm);
    }

    public function indexAction()
    {
       $holidayList= $this->repository->fetchAll();
       $viewModel= new ViewModel(Helper::addFlashMessagesToArray($this, [
            'holidayList' => $holidayList,
        ]));
        return $viewModel;
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $holiday = new Holiday();
                $holiday->exchangeArrayFromForm($this->form->getData());
                $holiday->holidayId = ((int)Helper::getMaxId($this->adapter, "HR_HOLIDAY_MASTER_SETUP", "HOLIDAY_ID")) + 1;
                $holiday->createdDt = Helper::getcurrentExpressionDate();

                $holiday->status = 'E';
                $holiday->fiscalYear=(int) Helper::getMaxId($this->adapter,"HR_FISCAL_YEARS","FISCAL_YEAR_ID");
                $this->repository->add($holiday);
                $this->flashmessenger()->addMessage("Holiday Successfully added!!!");
                return $this->redirect()->toRoute("holidaysetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'customRenderer' => Helper::renderCustomView(),
                "genders" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_GENDERS),
                'branches' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_BRANCHES),
            ]
        )
        );
    }

    public function editAction()
    {
        $this->initializeForm();
        $id = (int)$this->params()->fromRoute("id");

        if ($id === 0) {
            return $this->redirect()->toRoute("holidaysetup");
        }

        $request = $this->getRequest();
        $holiday = new Holiday();
        if (!$request->isPost()) {
            $holiday->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($holiday);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $holiday->exchangeArrayFromForm($this->form->getData());
                $holiday->modifiedDt = Helper::getcurrentExpressionDate();
                unset($holiday->holidayId);
                unset($holiday->createdDt);
                unset($holiday->status);

                $this->repository->edit($holiday, $id);
                $this->flashmessenger()->addMessage("holiday Successfuly Updated!!!");
                return $this->redirect()->toRoute("holidaysetup");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'id'=>$id,
                'customRenderer' => Helper::renderCustomView(),
                "genders" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_GENDERS),
                'branches' => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_BRANCHES),
            ]));
    }

    public function deleteAction(){
        $id = (int)$this->params()->fromRoute("id");

        if ($id === 0) {
            return $this->redirect()->toRoute("holidaysetup");
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Holiday Successfully Deleted!!!");
        return $this->redirect()->toRoute('holidaysetup');

    }

}