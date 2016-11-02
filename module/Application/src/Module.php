<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as DbTableAuthAdapter;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface
{
    const VERSION = '3.0.1dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
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

    function beforeDispatch(MvcEvent $event)
    {

        $request = $event->getRequest();
        $response = $event->getResponse();
        $target = $event->getTarget();

        /*Offline pages not needed authentication*/
        $whiteList = [
            Controller\AuthController::class . '-login',
            Controller\AuthController::class . '-logout',
            Controller\AuthController::class . '-authenticate',
        ];
        $app = $event->getApplication();
        //$routeMatch = $event->getRouteMatch();
        $auth = $app->getServiceManager()->get('AuthService');

        $requestUri = $request->getRequestUri();
        $controller = $event->getRouteMatch()->getParam('controller');
        $action = $event->getRouteMatch()->getParam('action');

        $requestedResourse = $controller . "-" . $action;

        if (!$auth->hasIdentity() && !in_array($requestedResourse, $whiteList)) {
            $response = $event->getResponse();
            $response->getHeaders()->addHeaderLine(
                'Location',
                $event->getRouter()->assemble(
                    [],
                    ['name' => 'login']
                )
            );
            $response->setStatusCode(302);
            $response->sendHeaders();
            return $response;
        }

        //print "Called before any controller action called. Do any operation.";
    }

    function afterDispatch(MvcEvent $event)
    {
        //print "Called after any controller action called. Do any operation.";
    }

    public function getAutoloaderConfig()
    {
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\HrisAuthStorage::class => function ($container) {
                    return new Model\HrisAuthStorage();
                },

                'AuthService' => function ($container) {
                    $dbAdapter = $container->get(DbAdapter::class);
                    //$dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'users', 'username', 'password', 'MD5(?)');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'HR_USERS', 'USER_NAME', 'PASSWORD');

//                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'HR_EMPLOYEE_JOB_HISTORY', 'DESIGNATION_CODE', 'GRADE_CODE');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($container->get(Model\HrisAuthStorage::class));

                    return $authService;
                },
            ],

        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\AuthController::class => function ($container) {
                    return new Controller\AuthController(
                        $container->get('AuthService')
                    );
                },
            ],
        ];
    }
}
