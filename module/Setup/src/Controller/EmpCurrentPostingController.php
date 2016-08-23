<?php
namespace Setup\Controller;

/**
 * Master Setup for Employee Current Posting
 * Employee Current Posting controller.
 * Created By: Somkala Pachhai
 * Edited By:
 * Date: August 12, 2016, Friday
 * Last Modified By:
 * Last Modified Date:
 */


use Application\Helper\Helper;
use Setup\Form\EmpCurrentPostingForm;
use Setup\Helper\EntityHelper;
use Setup\Model\EmpCurrentPosting;
use Setup\Repository\EmpCurrentPostingRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;


class EmpCurrentPostingController extends AbstractActionController
{

    private $form;
    private $adapter;
    private $repository;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->repository = new EmpCurrentPostingRepository($adapter);
    }

    public function initializeForm()
    {
        $empCurrentPostingForm = new EmpCurrentPostingForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($empCurrentPostingForm);
        }
    }

    public function indexAction()
    {
        $empCurrentPostingList = $this->repository->fetchAll();
        return Helper::addFlashMessagesToArray($this, ['empCurrentPostingList' => $empCurrentPostingList]);
    }

    public function addAction()
    {
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $empCurrentPosting=new EmpCurrentPosting();
                $empCurrentPosting->exchangeArrayFromForm($this->form->getData());
                $this->repository->add($empCurrentPosting);

                $this->flashmessenger()->addMessage("Employee Current Posting Successfully added!!!");
                return $this->redirect()->toRoute("empCurrentPosting");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,
            [
                'form' => $this->form,
                'messages' => $this->flashmessenger()->getMessages(),
                'departments' => EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_DEPARTMENTS),
                'designations' => EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_DESIGNATIONS),
                'branches' => EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_BRANCHES),
                'positions' => EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_POSITIONS),
                'serviceTypes' => EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_SERVICE_TYPES),
            ]);
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('empCurrentPosting');
        }
        $this->initializeForm();
        $request = $this->getRequest();
        $empCurrentPosting=new EmpCurrentPosting();
        if (!$request->isPost()) {
            $empCurrentPosting->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($empCurrentPosting);
        } else {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $empCurrentPosting->exchangeArrayFromForm($this->form->getData());
                $this->repository->edit($empCurrentPosting, $id);

                $this->flashmessenger()->addMessage("Employee Current Posting Updated!!!");
                return $this->redirect()->toRoute("empCurrentPosting");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,
            [
                'id' => $id,
                'form' => $this->form,
                'messages' => $this->flashmessenger()->getMessages(),
                'departments' => EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_DEPARTMENTS),
                'designations' => EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_DESIGNATIONS),
                'branches' => EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_BRANCHES),
                'positions' => EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_POSITIONS),
                'serviceTypes' => EntityHelper::getTableKVList($this->adapter,EntityHelper::HR_SERVICE_TYPES),
            ]
        );
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('empCurrentPosting');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Employee Current Posting Deleted!!!");
        return $this->redirect()->toRoute("empCurrentPosting");
    }
}

/* End of file EmpCurrentPostingController.php */
/* Location: ./Setup/src/Controller/EmpCurrentPostingController.php */