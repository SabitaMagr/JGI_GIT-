<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 10/3/16
 * Time: 1:36 PM
 */

namespace Payroll\Controller;


use Application\Helper\ConstraintHelper;
use Application\Helper\Helper;
use Payroll\Repository\FlatValueRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Payroll\Model\FlatValue as FlatValueModel;

class FlatValue extends AbstractActionController
{

    private $adapter;
    private $repository;
    private $form;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new FlatValueRepository($adapter);
    }

    public function initializeForm()
    {
        $builder = new AnnotationBuilder();
        $flatValueForm = new \Payroll\Form\FlatValue();
        $this->form = $builder->createForm($flatValueForm);
    }

    public function indexAction()
    {
        return Helper::addFlashMessagesToArray($this, [
            'flatValues' => $this->repository->fetchAll(),
            'constraint' => ConstraintHelper::CONSTRAINTS['YN']
        ]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $flatValue = new FlatValueModel();
                $flatValue->exchangeArrayFromForm($this->form->getData());
                $flatValue->flatId = ((int)Helper::getMaxId($this->adapter, FlatValueModel::TABLE_NAME, FlatValueModel::FLAT_ID)) + 1;
                $flatValue->createdDt = Helper::getcurrentExpressionDate();
                $flatValue->status = 'E';

                $this->repository->add($flatValue);
                $this->flashmessenger()->addMessage("Flat Value added Successfully!!");
                return $this->redirect()->toRoute("flatValue");
            }
        }
        return Helper::addFlashMessagesToArray($this,
            [
                'form' => $this->form,
                'customRenderer' => Helper::renderCustomView()
            ]
        );

    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        $this->initializeForm();
        $request = $this->getRequest();

        $flatValueModel = new FlatValueModel();
        if (!$request->isPost()) {
            $flatValueModel->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($flatValueModel);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $flatValueModel->exchangeArrayFromForm($this->form->getData());
                $flatValueModel->modifiedDt = Helper::getcurrentExpressionDate();
                unset($flatValueModel->createdDt);
                unset($flatValueModel->flatId);
                unset($flatValueModel->status);
                $this->repository->edit($flatValueModel, $id);
                $this->flashmessenger()->addMessage("Flat Value updated successfully!!!");
                return $this->redirect()->toRoute("flatValue");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
            'form' => $this->form,
            'id' => $id,
            'customRenderer' => Helper::renderCustomView()
        ]);

    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('flatValue');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Flat Value Successfully Deleted!!!");
        return $this->redirect()->toRoute('flatValue');
    }
}