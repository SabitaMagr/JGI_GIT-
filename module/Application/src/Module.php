<?php

namespace Application;

use Application\Controller\AuthController;
use Application\Controller\ForgotPasswordController;
use Application\Factory\HrLogger;
use Application\Helper\Helper;
use Application\Helper\SessionHelper;
use Application\Model\HrisAuthStorage;
use DateTime;
use Interop\Container\ContainerInterface;
use Notification\Controller\HeadNotification;
use RestfulService\Controller\RestfulService;
use System\Model\MenuSetup;
use System\Model\Setting;
use System\Repository\DashboardDetailRepo;
use System\Repository\RolePermissionRepository;
use System\Repository\SettingRepository;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Adapter\AdapterInterface as DbAdapterInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class Module implements AutoloaderProviderInterface, ConsoleUsageProviderInterface {

    const VERSION = '3.0.1dev';

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

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
        $response = $event->getResponse();

        /* Offline pages not needed authentication */
        $whiteList = [
            AuthController::class . '-login',
            AuthController::class . '-logout',
            AuthController::class . '-authenticate',
            RestfulService::class . '-restful',
            ForgotPasswordController::class . "-checkCodeDetail",
            ForgotPasswordController::class . "-index",
            ForgotPasswordController::class . "-code",
            ForgotPasswordController::class . "-password",
            ForgotPasswordController::class . "-email",
            Controller\CronController::class . '-index',
            Controller\CronController::class . '-employee-attendance',
        ];
        $app = $event->getApplication();
        $auth = $app->getServiceManager()->get('AuthService');

        $controller = $event->getRouteMatch()->getParam('controller');
        $action = $event->getRouteMatch()->getParam('action');
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
        $route = $event->getRouteMatch()->getMatchedRouteName();
        $identity = $auth->getIdentity();
        
        if(is_array($identity)){
        $roleId = $identity['role_id'];
        }else{
        $roleId = null;
        }
        
        if ($roleId != null) {
            $adapter = $app->getServiceManager()->get(DbAdapterInterface::class);
            $repository = new RolePermissionRepository($adapter);
            $data = $repository->fetchAllMenuByRoleId($roleId);
            $allowFlag = false;
            $allowedRoutes = ['application', "home", 'auth', 'login', 'logout', 'checkout', 'restful', 'user-setting', 'webService'];
            if (in_array($route, $allowedRoutes)) {
                $allowFlag = true;
            }
            foreach ($data as $d) {
                if ($d[MenuSetup::ROUTE] == $route) {
                    $allowFlag = true;
                    break;
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
            SessionHelper::sessionCheck($event);
            $this->initNotification($adapter, $event->getViewModel(), $identity);

            $dashboardDetailRepo = new DashboardDetailRepo($adapter);
            $rawResult = $dashboardDetailRepo->fetchById($roleId);
            $result = Helper::extractDbData($rawResult, true);

            $type = null;
            foreach ($result as $dashboard) {
                if ($dashboard['DASHBOARD'] == 'dashboard') {
                    $type = $dashboard['ROLE_TYPE'];
                    break;
                }
            }


            $event->getViewModel()->setVariable('displayGlobalSearch', ($type == "A") ? 1 : 0);
            if ($type == "A") {
                $employeeRepo = new \Setup\Repository\EmployeeRepository($adapter);
                $event->getViewModel()->setVariable('employeeList', $employeeRepo->fetchEmployeeFullNameList());
            }
        }



        //print "Called before any controller action called. Do any operation.";
    }

    private function initNotification(DbAdapterInterface $adapter, ViewModel $viewModel, array $identity = null) {
        $employeeId = $identity['employee_id'];
        $viewModel->setVariable('dateCompare', function($date) {
            $startDate = DateTime::createFromFormat(Helper::PHP_DATE_FORMAT . " " . Helper::PHP_TIME_FORMAT, $date);
            $currentDate = new DateTime();
            $interval = $startDate->diff($currentDate);
            return $interval->d;
        });
        if ($employeeId == null) {
            $viewModel->setVariable("notifications", []);
        } else {
            $settingRepo = new SettingRepository($adapter);
            $userSetting = $settingRepo->fetchById($identity['user_id']);
            if ($userSetting == null || ($userSetting[Setting::ENABLE_NOTIFICATION] == 'Y')) {
                $viewModel->setVariable("notifications", HeadNotification::getNotifications($adapter, $employeeId));
            } else {
                $viewModel->setVariable("notifications", []);
            }
        }
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
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'HRIS_USERS', 'USER_NAME', 'PASSWORD');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($container->get(HrisAuthStorage::class));

                    return $authService;
                },
                HrLogger::class => function(ContainerInterface $container) {
                    return HrLogger::getInstance();
                }
            ],
        ];
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                AuthController::class => function ($container) {
                    return new AuthController($container->get('AuthService'), $container->get(DbAdapterInterface::class));
                },
            ],
        ];
    }

    public function getConsoleUsage(AdapterInterface $console) {
        return [
            'attendance daily-attendance' => 'Daily Attendance',
            'attendance employee-attendance <employeeId> <attendanceDt> <attendanceTime>' => 'Employee Daily Attendance'
        ];
    }

}
