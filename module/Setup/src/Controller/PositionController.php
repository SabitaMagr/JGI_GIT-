<?php

namespace Setup\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Setup\Form\PositionForm;
use Setup\Model\Company;
use Setup\Model\Position;
use Setup\Repository\PositionRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PositionController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->repository = new PositionRepository($adapter);
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $positionForm = new PositionForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($positionForm);
        }
    }

    public function indexAction() {
        $positionList = $this->repository->fetchActiveRecord();
        return Helper::addFlashMessagesToArray($this, ['positions' => $positionList]);
    }

    public function addAction() {
//        $tableName = "HRIS_POSITIONS";
//        $columnsWidValues = ["POSITION_NAME"=>"Junior Officer"];
//
//        $result = ConstraintHelper::checkUniqueConstraint($this->adapter, $tableName, $columnsWidValues);
//        die ();

        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $position = new Position();
                $position->exchangeArrayFromForm($this->form->getData());
                $position->positionId = ((int) Helper::getMaxId($this->adapter, Position::TABLE_NAME, Position::POSITION_ID)) + 1;
                $position->createdDt = Helper::getcurrentExpressionDate();
                $position->createdBy = $this->employeeId;
                $position->status = 'E';
                $this->repository->add($position);

                $this->flashmessenger()->addMessage("Position Successfully added!!!");
                return $this->redirect()->toRoute("position");
            }
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC"),
                    'messages' => $this->flashmessenger()->getMessages()
                        ]
                )
        );
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('position');
        }

        $this->initializeForm();
        $request = $this->getRequest();

        $position = new Position();
        if (!$request->isPost()) {
            $position->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($position);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $position->exchangeArrayFromForm($this->form->getData());
                $position->modifiedDt = Helper::getcurrentExpressionDate();
                $position->modifiedBy = $this->employeeId;
                $this->repository->edit($position, $id);
                $this->flashmessenger()->addMessage("Position Successfully Updated!!!");
                return $this->redirect()->toRoute("position");
            }
        }
        return Helper::addFlashMessagesToArray(
                        $this, [
                            'form' => $this->form,
                            'companies' => EntityHelper::getTableKVListWithSortOption($this->adapter, Company::TABLE_NAME, Company::COMPANY_ID, [Company::COMPANY_NAME], ["STATUS" => "E"], Company::COMPANY_NAME, "ASC"),
                            'id' => $id
                ]
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('position');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Position Successfully Deleted!!!");
        return $this->redirect()->toRoute('position');
    }

}

/* End of file PositionController.php */
/* Location: ./Setup/src/Controller/PositionController.php */
?>