<?php

namespace WorkOnHoliday\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use HolidayManagement\Model\Holiday;
use SelfService\Form\WorkOnHolidayForm;
use SelfService\Model\WorkOnHoliday;
use SelfService\Repository\WorkOnHolidayRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

;

class WorkOnHolidayApply extends AbstractActionController {

    private $form;
    private $adapter;
    private $workOnHolidayRepository;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->workOnHolidayRepository = new WorkOnHolidayRepository($adapter);
    }

    public function initializeForm() {
        $builder = new AnnotationBuilder();
        $form = new WorkOnHolidayForm();
        $this->form = $builder->createForm($form);
    }

    public function indexAction() {
        return $this->redirect()->toRoute("workOnHolidayStatus");
    }

    public function addAction() {
        $this->initializeForm();
        $request = $this->getRequest();

        $model = new WorkOnHoliday();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $model->exchangeArrayFromForm($this->form->getData());
                $model->id = ((int) Helper::getMaxId($this->adapter, WorkOnHoliday::TABLE_NAME, WorkOnHoliday::ID)) + 1;
                $model->requestedDate = Helper::getcurrentExpressionDate();
                $model->status = 'RQ';
                $this->workOnHolidayRepository->add($model);
                $this->flashmessenger()->addMessage("Work on Holiday Request Successfully added!!!");
                return $this->redirect()->toRoute("workOnHolidayStatus");
            }
        }

        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'employees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["FIRST_NAME", "MIDDLE_NAME", "LAST_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N'], "FIRST_NAME", "ASC", " ", false, true),
                    'holidays' => EntityHelper::getTableKVListWithSortOption($this->adapter, Holiday::TABLE_NAME, Holiday::HOLIDAY_ID, [Holiday::HOLIDAY_ENAME], ["STATUS" => 'E'], "HOLIDAY_ENAME", "ASC", null, false, true)
        ]);
    }

}
