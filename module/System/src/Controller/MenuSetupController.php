<?php

namespace System\Controller;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use System\Form\MenuSetupForm;
use System\Model\MenuSetup;
use System\Model\RolePermission;
use System\Repository\MenuSetupRepository;
use System\Repository\RolePermissionRepository;
use System\Repository\RoleSetupRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class MenuSetupController extends AbstractActionController {

    private $repository;
    private $adapter;
    private $form;
    private $employeeId;

    public function __construct(AdapterInterface $adapter) {
        $this->repository = new MenuSetupRepository($adapter);
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->employeeId = $auth->getStorage()->read()['employee_id'];
    }

    public function initializeForm() {
        $menuSetupForm = new MenuSetupForm();
        $builder = new AnnotationBuilder();
        $this->form = $builder->createForm($menuSetupForm);
    }

    public function indexAction() {
        $list = $this->repository->fetchAll();

        $request = $this->getRequest();
        $this->initializeForm();

        $menuList = EntityHelper::getTableKVList($this->adapter, MenuSetup::TABLE_NAME, MenuSetup::MENU_ID, [MenuSetup::MENU_NAME], [MenuSetup::STATUS => "E"]);
        ksort($menuList);

        $roleSetupRepository = new RoleSetupRepository($this->adapter);
        $roleList = $roleSetupRepository->fetchAll();

        if ($request->isPost()) {
            $menuSetup = new MenuSetup();
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $menuSetup->exchangeArrayFromForm($this->form->getData());
                $menuSetup->menuId = ((int) Helper::getMaxId($this->adapter, MenuSetup::TABLE_NAME, MenuSetup::MENU_ID)) + 1;
                $menuSetup->createdDt = Helper::getcurrentExpressionDate();
                $menuSetup->createdBy = $this->employeeId;
                $menuSetup->status = 'E';
                $this->repository->add($menuSetup);

                $this->flashmessenger()->addMessage("Menu Successfully Added!!!");
                return $this->redirect()->toRoute("menusetup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'menuList' => $menuList,
                    "list" => $list,
                    "roleList" => $roleList
        ]);
    }

    public function addAction() {
        $request = $this->getRequest();
        $this->initializeForm();

        $menuList = EntityHelper::getTableKVList($this->adapter, MenuSetup::TABLE_NAME, MenuSetup::MENU_ID, [MenuSetup::MENU_NAME], [MenuSetup::STATUS => "E"]);
        $menuList[-1] = "None";
        ksort($menuList);

        if ($request->isPost()) {
            $menuSetup = new MenuSetup();
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $menuSetup->exchangeArrayFromForm($this->form->getData());
                $menuSetup->menuId = ((int) Helper::getMaxId($this->adapter, MenuSetup::TABLE_NAME, MenuSetup::MENU_ID)) + 1;
                $menuSetup->createdDt = Helper::getcurrentExpressionDate();
                $menuSetup->createdBy = $this->employeeId;
                $menuSetup->status = 'E';
                $this->repository->add($menuSetup);

                $this->flashmessenger()->addMessage("Menu Successfully Added!!!");
                return $this->redirect()->toRoute("menusetup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    'menuList' => $menuList
        ]);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute("id");
        $this->initializeForm();
        $request = $this->getRequest();

        $menuList = $this->repository->getMenuList($id);

        $menuSetup = new MenuSetup();
        if (!$request->isPost()) {
            $detail = $this->repository->fetchById($id)->getArrayCopy();
            $menuSetup->exchangeArrayFromDB($detail);
            $this->form->bind($menuSetup);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $menuSetup->exchangeArrayFromForm($this->form->getData());
                $menuSetup->modifiedDt = Helper::getcurrentExpressionDate();
                $menuSetup->modifiedBy = $this->employeeId;
                unset($menuSetup->createdDt);
                unset($menuSetup->menuId);
                unset($menuSetup->status);
                $this->repository->edit($menuSetup, $id);
                $this->flashmessenger()->addMessage("Menu Successfully Updated!!!");
                return $this->redirect()->toRoute("menusetup");
            }
        }
        return Helper::addFlashMessagesToArray($this, [
                    'id' => $id,
                    'form' => $this->form,
                    'menuList' => $menuList
        ]);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute("id");

        if (!$id) {
            return $this->redirect()->toRoute('menusetup');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Menu Successfully Deleted!!!");
        return $this->redirect()->toRoute('menusetup');
    }

    public function menuAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            return new JsonModel(['success' => true, 'data' => $this->menu(), 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function menuInsertionAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $record = $data['dataArray'];
            $model = new MenuSetup();
            $repository = new MenuSetupRepository($this->adapter);
            $model->menuId = Helper::getMaxId($this->adapter, MenuSetup::TABLE_NAME, MenuSetup::MENU_ID) + 1;
            $model->menuCode = $record['menuCode'];
            $model->menuName = $record['menuName'];
            $model->route = $record['route'];
            $model->action = $record['action'];
            $model->menuIndex = $record['menuIndex'];
            $model->iconClass = $record['iconClass'];
            if ($data['parentMenu'] != null) {
                $model->parentMenu = $data['parentMenu'];
            }
            $model->menuDescription = $record['menuDescription'];
            $model->isVisible = $record['isVisible'];
            $model->status = 'E';
            $model->createdDt = Helper::getcurrentExpressionDate();
            $model->createdBy = $this->employeeId;

            $menuIndexErr = "";
            $repository->add($model);
            $data = "Menu Successfully Added!!";
            $menuData = $this->menu();

            return new JsonModel([
                "success" => true,
                "data" => $data,
                "menuData" => $menuData,
                "menuIndexErr" => $menuIndexErr
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function menuUpdateAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $record = $data['dataArray'];
            $model = new MenuSetup();
            $repository = new MenuSetupRepository($this->adapter);
            $menuId = $record['menuId'];
            $model->modifiedDt = Helper::getcurrentExpressionDate();
            $model->modifiedBy = $this->employeeId;
            $model->menuCode = $record['menuCode'];
            $model->menuName = $record['menuName'];
            $model->route = $record['route'];
            $model->action = $record['action'];
            $model->menuIndex = $record['menuIndex'];
            $model->iconClass = $record['iconClass'];

            $model->menuDescription = $record['menuDescription'];
            $model->isVisible = $record['isVisible'];

            unset($model->status);
            unset($model->parentMenu);
            unset($model->menuId);
            unset($model->createdDt);

            $menuIndexErr = "";
            $repository->edit($model, $menuId);
            $data = "Menu Successfully Updated!!";
            $menuData = $this->menu();

            return new JsonModel([
                "success" => true,
                "data" => $data,
                "menuData" => $menuData,
                "menuIndexErr" => $menuIndexErr
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    private function menu($parent_menu = null) {
        $menuSetupRepository = new MenuSetupRepository($this->adapter);
        $result = $menuSetupRepository->getHierarchicalMenu($parent_menu);
        $num = count($result);
        if ($num > 0) {
            $temArray = array();
            foreach ($result as $row) {
                $children = $this->menu($row['MENU_ID']);
                if ($children) {
                    $temArray[] = array(
                        "text" => $row['MENU_NAME'],
                        "id" => $row['MENU_ID'],
                        "icon" => "fa fa-folder icon-state-success",
                        "children" => $children
                    );
                } else {
                    $temArray[] = array(
                        "text" => $row['MENU_NAME'],
                        "id" => $row['MENU_ID'],
                        "icon" => "fa fa-folder icon-state-success"
                    );
                }
            }
            return $temArray;
        } else {
            return false;
        }
    }

    public function pullMenuDetailAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $menuId = $data['id'];
            $repository = new MenuSetupRepository($this->adapter);
            $result = $repository->fetchById($menuId);


            return new JsonModel(['success' => true, 'data' => $result, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function permissionAssignAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();

            $rolePermissionRepository = new RolePermissionRepository($this->adapter);
            $menuSetupRepository = new MenuSetupRepository($this->adapter);
            $rolePermissionModel = new RolePermission();

            $roleId = $data['roleId'];
            $menuId = $data['menuId'];
            $checked = $data['checked'];

//if child of same parent menu were assigned on same roleId then don't need to deactivate parent menu list
            $menuDtl = $menuSetupRepository->fetchById($menuId);
            $menuListOfSameParent = $menuSetupRepository->getMenuListOfSameParent($menuDtl['PARENT_MENU']);
            $numMenuListOfSameParent = 0;
            foreach ($menuListOfSameParent as $childOfSameParent) {
                $existChildDtl = $rolePermissionRepository->getActiveRoleMenu($childOfSameParent['MENU_ID'], $roleId);
                if ($existChildDtl) {
                    $numMenuListOfSameParent += 1;
                }
            }

            $childMenuList = $menuSetupRepository->getAllCHildMenu($menuId);
            $parentMenuList = $menuSetupRepository->getAllParentMenu($menuId);

            if ($checked == "true") {
                foreach ($childMenuList as $row) {

                    $result = $rolePermissionRepository->selectRoleMenu($row['MENU_ID'], $roleId);
//$num = count($result);
                    if ($result) {
                        $rolePermissionRepository->updateDetail($row['MENU_ID'], $roleId);
                    } else {

                        $rolePermissionModel->roleId = $roleId;
                        $rolePermissionModel->menuId = $row['MENU_ID'];
                        $rolePermissionModel->createdDt = Helper::getcurrentExpressionDate();
                        $rolePermissionModel->status = 'E';

                        $rolePermissionRepository->add($rolePermissionModel);
                    }
                }
                foreach ($parentMenuList as $row) {

                    $result = $rolePermissionRepository->selectRoleMenu($row['MENU_ID'], $roleId);
//$num = count($result);
                    if ($result) {
                        $rolePermissionRepository->updateDetail($row['MENU_ID'], $roleId);
                    } else {

                        $rolePermissionModel->roleId = $roleId;
                        $rolePermissionModel->menuId = $row['MENU_ID'];
                        $rolePermissionModel->createdDt = Helper::getcurrentExpressionDate();
                        $rolePermissionModel->status = 'E';

                        $rolePermissionRepository->add($rolePermissionModel);
                    }
                }
                $data = "Role Successfully Assigned";
            } else if ($checked == "false") {
                foreach ($childMenuList as $row) {
                    $rolePermissionRepository->deleteAll($row['MENU_ID'], $roleId);
                }
                if ($numMenuListOfSameParent == 1) {
                    foreach ($parentMenuList as $row) {
                        $rolePermissionRepository->deleteAll($row['MENU_ID'], $roleId);

//need to activate those parent key whose another child key is assigned on same roleId
                        $childMenuList1 = $menuSetupRepository->getMenuListOfSameParent($row['MENU_ID']);
                        foreach ($childMenuList1 as $childRow) {
                            $getPermissionDtl = $rolePermissionRepository->getActiveRoleMenu($childRow['MENU_ID'], $roleId);
                            if ($getPermissionDtl) {
                                $rolePermissionRepository->updateDetail($row['MENU_ID'], $roleId);
                            }
                        }
                    }
                } else {
                    $rolePermissionRepository->deleteAll($menuId, $roleId);
                }
                $data = "Role Assign Successfully Removed";
            }

            return new JsonModel(['success' => true, 'data' => $data, 'message' => null]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

    public function pullRolePermissionListAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();


            $menuId = $data['menuId'];

            $rolePermissionRepository = new RolePermissionRepository($this->adapter);
            $roleRepository = new RoleSetupRepository($this->adapter);

            $result = $roleRepository->fetchAll();
            $rolePermissionList = $rolePermissionRepository->findAllRoleByMenuId($menuId);

            $tempArray = [];
            foreach ($result as $item) {
                array_push($tempArray, $item);
            }

            $temArray1 = [];
            foreach ($rolePermissionList as $row) {
                array_push($temArray1, $row);
            }


            return new JsonModel([
                "success" => true,
                "data" => $tempArray,
                "data1" => $temArray1
            ]);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => null, 'message' => $e->getMessage()]);
        }
    }

}
