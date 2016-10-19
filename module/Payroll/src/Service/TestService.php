<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/16/16
 * Time: 1:19 PM
 */

namespace Payroll\Service;

use Thread;

class TestService extends Thread
{
    public $data="data before";
    public function run()
    {
        $data="data after";
    }

}