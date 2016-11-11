<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Controller\AuthController;
use Application\Model\HrisAuthStorage;
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
        ];
        $app = $event->getApplication();
        //$routeMatch = $event->getRouteMatch();
        $auth = $app->getServiceManager()->get('AuthService');

        $requestUri = $request->getRequestUri();
        $controller = $event->getRouteMatch()->getParam('controller');
        $action = $event->getRouteMatch()->getParam('action');
        $route=$event->getRouteMatch()->getMatchedRouteName();
        
        
        print "<pre>";
        $auth = new AuthenticationService();
        $roleId=$auth->getStorage()->read()['role_id'];
        print $roleId;

        $repository = new RolePermissionRepository($app->getServiceManager()->get(DbAdapterInterface::class));
        $data = $repository->fetchAllMenuByRoleId($roleId);
        $allowFlag=false;
        foreach ($data as $d) {
            if($d[MenuSetup::ROUTE] == $route){
                print "allowed";
                $allowFlag=true;
                break;
            }
        }
        
        if(!$allowFlag){
           $response = $event->getResponse();
            $response->getHeaders()->addHeaderLine(
                    'Location', $event->getRouter()->assemble(
                            [], ['name' => 'home']
                    )
            );
            print_r($event->getRouter()->assemble(
                            ['action'=>'accessDenied'], ['name' => 'application']
                    ));
//            $response->setStatusCode(302);
//            $response->sendHeaders();
//            return $response;    
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
            // Describe available commands
            'user resetpassword [--verbose|-v] EMAIL' => 'Reset password for a user',
            // Describe expected parameters
            ['EMAIL', 'Email of the user for a password reset'],
                ['--verbose|-v', '(optional) turn on verbose mode'],
        ];
    }

}
