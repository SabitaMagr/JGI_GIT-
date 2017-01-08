<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
return [
    'Zend\Navigation',
    'Zend\Db',
    'Zend\ServiceManager\Di',
    'Zend\Session',
    'Zend\Mvc\Plugin\Prg',
    'Zend\Mvc\Plugin\Identity',
    'Zend\Mvc\Plugin\FlashMessenger',
    'Zend\Mvc\Plugin\FilePrg',
    'Zend\Mvc\I18n',
    'Zend\Log',
    'Zend\Form',
    'Zend\Cache',
    'Zend\Router',
    'Zend\Validator',
    'Zend\Navigation',
    'Zend\Mvc\Console',
    'Application',
    'Setup',
    'LeaveManagement',
    'HolidayManagement',
    'AttendanceManagement',
    'SelfService',
    'RestfulService',
    'Payroll',
    'ManagerService',
    'System',
    'Training',
    'Appraisal',
    'Loan'
        // These are various options for the listeners attached to the ModuleManager
        // 'module_listener_options' => [
        //     'module_paths' => [
        //         './module',
        //         './vendor',
        //     ],
        //     'config_glob_paths' => [
        //         'config/autoload/{,*.}{global,local}.php',
        //     ],
        // ],
];



