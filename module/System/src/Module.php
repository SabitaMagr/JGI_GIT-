<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/17/16
 * Time: 1:11 PM
 */
namespace System;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface {

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . "/../config/module.config.php";
    }
}