<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Controller\AuthController;
use Application\Helper\EntityHelper;
use Application\Model\HrisAuthStorage;
use RestfulService\Controller\RestfulService;
use System\Model\MenuSetup;
use System\Repository\RolePermissionRepository;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Adapter\AdapterInterface as DbAdapterInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface, ConsoleUsageProviderInterface {

    const VERSION = '3.0.1dev';

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $serviceManager = $e->getApplication()->getServiceManager();

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, [
            $this,
            'beforeDispatch'
                ], 100);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, [
            $this,
            'afterDispatch'
                ], -100);
    }

    function beforeDispatch(MvcEvent $event) {

        $request = $event->getRequest();

        if ($request->getContent() != null) {
            return;
        }
        $response = $event->getResponse();
        $target = $event->getTarget();

        /* Offline pages not needed authentication */
        $whiteList = [
            AuthController::class . '-login',
            AuthController::class . '-logout',
            AuthController::class . '-authenticate',
            RestfulService::class . '-restful'
        ];
        $app = $event->getApplication();
        //$routeMatch = $event->getRouteMatch();
        $auth = $app->getServiceManager()->get('AuthService');

        $requestUri = $request->getRequestUri();
        $controller = $event->getRouteMatch()->getParam('controller');
        $action = $event->getRouteMatch()->getParam('action');
        $route = $event->getRouteMatch()->getMatchedRouteName();


        $auth = new AuthenticationService();
        $roleId = $auth->getStorage()->read()['role_id'];

        if ($roleId != null) {
            $adapter = $app->getServiceManager()->get(DbAdapterInterface::class);
            $repository = new RolePermissionRepository($adapter);
            $data = $repository->fetchAllMenuByRoleId($roleId);
            $allowFlag = false;
            foreach ($data as $d) {
                if ($d[MenuSetup::ROUTE] == $route) {
                    $allowFlag = true;
                    break;
                } else if ($route == 'application' || $route == "home" || $route == 'auth' || $route == 'login' || $route == 'logout' || $route == 'restful') {
                    $allowFlag = true;
                }
            }
            if (!$allowFlag) {
                $response = $event->getResponse();
                $response->getHeaders()->addHeaderLine(
                        'Location', $event->getRouter()->assemble(
                                ['action' => 'accessDenied'], ['name' => 'application']
                        )
                );
                $response->setStatusCode(302);
                $response->sendHeaders();
                return $response;
            }
        }

        $requestedResourse = $controller . "-" . $action;

        if (!$auth->hasIdentity() && !in_array($requestedResourse, $whiteList)) {
            $response = $event->getResponse();
            $response->getHeaders()->addHeaderLine(
                    'Location', $event->getRouter()->assemble(
                            [], ['name' => 'login']
                    )
            );
            $response->setStatusCode(302);
            $response->sendHeaders();
            return $response;
        }

        $employeeId = $auth->getStorage()->read()['employee_id'];
        if ($employeeId != null) {
            $employeeFileId = EntityHelper::getTableKVList($adapter, \Setup\Model\HrEmployees::TABLE_NAME, \Setup\Model\HrEmployees::EMPLOYEE_ID, [\Setup\Model\HrEmployees::PROFILE_PICTURE_ID], [\Setup\Model\HrEmployees::EMPLOYEE_ID => $employeeId], null)[$employeeId];
            $employeeName = EntityHelper::getTableKVList($adapter, \Setup\Model\HrEmployees::TABLE_NAME, \Setup\Model\HrEmployees::EMPLOYEE_ID, [\Setup\Model\HrEmployees::FIRST_NAME], [\Setup\Model\HrEmployees::EMPLOYEE_ID => $employeeId], null)[$employeeId];
            if ($employeeFileId != null) {
                $filePath = EntityHelper::getTableKVList($adapter, \Setup\Model\EmployeeFile::TABLE_NAME, \Setup\Model\EmployeeFile::FILE_CODE, [\Setup\Model\EmployeeFile::FILE_PATH], [\Setup\Model\EmployeeFile::FILE_CODE => $employeeFileId], null)[$employeeFileId];
                $event->getViewModel()->setVariable("profilePictureUrl", $filePath);
                $event->getViewModel()->setVariable("employeeName", $employeeName);
            } else {
                $event->getViewModel()->setVariable("profilePictureUrl", "1480316755.jpg");
                $event->getViewModel()->setVariable("employeeName", "Nick");
            }
        }

        //print "Called before any controller action called. Do any operation.";
    }

    function afterDispatch(MvcEvent $event) {
        //print "Called after any controller action called. Do any operation.";
    }

    public function getAutoloaderConfig() {
        
    }

    public function getServiceConfig() {
        return [
            'factories' => [
                HrisAuthStorage::class => function ($container) {
                    return new HrisAuthStorage();
                },
                'AuthService' => function ($container) {
                    $dbAdapter = $container->get(DbAdapter::class);
                    //$dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'users', 'username', 'password', 'MD5(?)');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'HR_USERS', 'USER_NAME', 'PASSWORD');

//                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'HR_EMPLOYEE_JOB_HISTORY', 'DESIGNATION_CODE', 'GRADE_CODE');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($container->get(HrisAuthStorage::class));

                    return $authService;
                },
            ],
        ];
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                AuthController::class => function ($container) {
                    return new AuthController(
                            $container->get('AuthService')
                    );
                },
            ],
        ];
    }

    public function getConsoleUsage(AdapterInterface $console) {
        return [
            'attendance daily-attendance' => 'Daily Attendance'
        ];
    }

}
