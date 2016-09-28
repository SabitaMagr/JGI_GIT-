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
use HolidayManagement\Model\HolidayBranch;
use Setup\Model\Branch;
use Zend\Form\Element\Select;

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
        $this->initializeForm();
        $holidayFormElement = new Select();
        $holidayFormElement->setName("branch");
        $holidays=\Application\Helper\EntityHelper::getTableKVList($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME]);
        ksort($holidays);
        $holidayFormElement->setValueOptions($holidays);
        $holidayFormElement->setAttributes(["id" => "holidayId", "class" => "form-control"]);
        $holidayFormElement->setLabel("Holiday");

        $branchFormElement = new Select();
        $branchFormElement->setName("branch");
        $branches=\Application\Helper\EntityHelper::getTableKVList($this->adapter, Branch::TABLE_NAME, Branch::BRANCH_ID, [Branch::BRANCH_NAME],["STATUS"=>"E"]);

        ksort($branches);
        $branchFormElement->setValueOptions($branches);
        $branchFormElement->setAttributes(["id" => "branchId", "class" => "form-control", "multiple"=>"multiple"]);
        $branchFormElement->setLabel("Branch");

        $genderFormElement = new Select();
        $genderFormElement->setName("gender");
        $genders=\Application\Helper\EntityHelper::getTableKVList($this->adapter,"HR_GENDERS","GENDER_ID" , ["GENDER_NAME"]);
        $genders[-1]="All";
        ksort($genders);

        $holidayList= $this->repository->fetchAll();
        $viewModel= new ViewModel(Helper::addFlashMessagesToArray($this, [
            'holidayList' => $holidayList,
            'holidayFormElement'=>$holidayFormElement,
            'branchFormElement'=>$branchFormElement,
            'genderFormElement'=>$genderFormElement,
            'form'=>$this->form,
            'customRenderer' => Helper::renderCustomView(),
            "genders" => EntityHelper::getTableKVList($this->adapter, EntityHelper::HR_GENDERS),
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
                $holidayBranch = new HolidayBranch();
                $holiday->exchangeArrayFromForm($this->form->getData());
                $holiday->createdDt = Helper::getcurrentExpressionDate();
                $holiday->status = 'E';
                $holiday->fiscalYear=(int) Helper::getMaxId($this->adapter,"HR_FISCAL_YEARS","FISCAL_YEAR_ID");

                $branches = $holiday->branchId;
                unset($holiday->branchId);

                $holiday->holidayId = ((int)Helper::getMaxId($this->adapter,'HR_HOLIDAY_MASTER_SETUP', 'HOLIDAY_ID')) + 1;
                $this->repository->add($holiday);

                foreach($branches as $branchId){
                    $holidayBranch->branchId = $branchId;
                    $holidayBranch->holidayId = $holiday->holidayId;
                    $this->repository->addHolidayBranch($holidayBranch);
                }
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