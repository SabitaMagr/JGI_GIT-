<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/13/16
 * Time: 11:01 AM
 */
namespace AttendanceManagement;

use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

class Module implements
    ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }


    

}