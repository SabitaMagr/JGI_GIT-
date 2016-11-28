<?php
/**
 * Created by PhpStorm.
 * User: punam
 * Date: 9/15/16
 * Time: 12:55 PM
 */

namespace SelfService;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}