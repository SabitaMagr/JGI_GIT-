<?php

namespace Setup\Controller;

use Application\Helper\Helper;
use Doctrine\ORM\EntityManager;
use Setup\Entity\Album;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Setup\Model\Branch;
use Zend\View\View;

class BranchController extends AbstractActionController
{

    private $branch;
    private $form;
    private $entityManager;

    function __construct(EntityManager $entityManager)
    {
            $this->entityManager=$entityManager;

//       print_r($adapter->query('SELECT * FROM `branch` WHERE `branchCode` = ?', [5])) ;
//        $sql = $adapter->query('SELECT * FROM `branch`',
//            Adapter::QUERY_MODE_EXECUTE);
//        print_r($sql->current());
//        die();

//        $statement = $adapter->query(
//            'SELECT * FROM branch where branchCode = ?',[1]
//        );

//            $statement= $adapter->createStatement('SELECT * FROM branch where branchCode = ?',[2]);
//        $results=$statement->execute();
//
//
//        $row = $results->current();
//        $name = $row['branchName'];
//
//        echo $name;
//        die();

//        $sql = new Sql($adapter);
//
//        $select = $sql->select();
//
//        $select
//            ->from(['b' => 'branch'])->join(
//                ['d' => 'designation'],
//                'b.branchcode = d.designationCode');
//        $statement = $sql->prepareStatementForSqlObject($select);
//        $result = $statement->execute();
//
//        forEach ($result as $r) {
//            print_r($r);
//        }
//        die();
    }


    public function initializeForm()
    {
        $this->branch = new Branch();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($this->branch);
        }
    }

    public function indexAction()
    {
//        $designation = new Album();
//        $designation->setTitle("sdsdff");
//        $designation->setArtist("ds");
//
//        $this->entityManager->persist($designation);
//        $this->entityManager->flush();


        $branchRepository = $this->entityManager->getRepository(Album::class);
        $branches = $branchRepository->findAll();
        return Helper::addFlashMessagesToArray($this, ['branches' => $branches]);
    }

    public function addAction()
    {
        $this->initializeForm();

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return Helper::addFlashMessagesToArray($this, ['form' => $this->form]);
        }

        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
            $this->branch->exchangeArray($this->form->getData());
            $this->repository->add($this->branch);
            $this->flashmessenger()->addMessage("Branch Successfully Added!!!");
            return $this->redirect()->toRoute("branch");
        } else {
            return Helper::addFlashMessagesToArray($this, ['form' => $this->form]);

        }
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        $this->initializeForm();

        $request = $this->getRequest();

        if (!$request->isPost()) {
            $this->form->bind($this->repository->fetchById($id));
            return Helper::addFlashMessagesToArray($this, ['form' => $this->form, 'id' => $id]);
        }


        $this->form->setData($request->getPost());

        if ($this->form->isValid()) {
            $this->branch->exchangeArray($this->form->getData());
            $this->repository->edit($this->branch, $id);
            $this->flashmessenger()->addMessage("Branch Successfully Updated!!!");
            return $this->redirect()->toRoute("branch");
        } else {
            return Helper::addFlashMessagesToArray($this, ['form' => $this->form, 'id' => $id]);

        }
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id");
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Branch Successfully Deleted!!!");
        return $this->redirect()->toRoute('branch');
    }

}