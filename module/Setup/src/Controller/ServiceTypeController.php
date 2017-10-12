<?php

namespace Setup\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\ServiceTypeForm;
use Setup\Model\ServiceType;
use Setup\Repository\ServiceTypeRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;

class ServiceTypeController extends AbstractActionController {

    private $repository;
    private $form;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        $this->adapter = $adapter;
        $this->repository = new ServiceTypeRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    private function initializeForm() {
        $serviceTypeForm = new ServiceTypeForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($serviceTypeForm);
        }
    }

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchActiveRecord();
                $serviceTypeList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $serviceTypeList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function addAction() {

        ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this);
        $this->initializeForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                try {
                    $serviceType = new ServiceType();
                    $serviceType->exchangeArrayFromForm($this->form->getData());
                    $serviceType->serviceTypeId = ((int) Helper::getMaxId($this->adapter, ServiceType::TABLE_NAME, ServiceType::SERVICE_TYPE_ID)) + 1;
                    $serviceType->createdDt = Helper::getcurrentExpressionDate();
                    $serviceType->createdBy = $this->employeeId;
                    $serviceType->status = 'E';
                    $this->repository->add($serviceType);

                    $this->flashmessenger()->addMessage("Service Type Successfully Added!!!");
                    return $this->redirect()->toRoute("serviceType");
                } catch (Exception $e) {
                    
                }
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages()
        ]);
    }

    public function editAction() {

        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute();
        }
        $this->initializeForm();
        $request = $this->getRequest();
        $serviceType = new ServiceType();
        if (!$request->isPost()) {
            $serviceType->exchangeArrayFromDb($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($serviceType);
        } else {

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $serviceType->exchangeArrayFromForm($this->form->getData());
                $serviceType->modifiedDt = Helper::getcurrentExpressionDate();
                $serviceType->modifiedBy = $this->employeeId;
                $this->repository->edit($serviceType, $id);
                $this->flashmessenger()->addMessage("Service Type Successfully Updated!!!");
                return $this->redirect()->toRoute("serviceType");
            }
        }
        return Helper::addFlashMessagesToArray($this, ['form' => $this->form, 'id' => $id]);
    }

    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('serviceType');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Service Type Successfully Deleted!!!");
        return $this->redirect()->toRoute('serviceType');
    }

}

/* End of file ServiceTypeController.php */
/* Location: ./Setup/src/Controller/ServiceTypeController.php */
?>

