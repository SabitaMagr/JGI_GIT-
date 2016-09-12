<?php

namespace HolidayManagement\Controller;

use Application\Helper\Helper;
use HolidayManagement\Form\HolidayForm;
use HolidayManagement\Model\Holiday;
use HolidayManagement\Repository\HolidayRepository;
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
                $this->repository->add($holiday);
                $this->flashmessenger()->addMessage("Holiday Successfully added!!!");
                return $this->redirect()->toRoute("holiday");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'customRenderer' => Helper::renderCustomView()
            ]
        )
        );
    }

}