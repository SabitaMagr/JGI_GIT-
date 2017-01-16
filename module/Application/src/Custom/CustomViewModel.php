<?php

namespace Application\Custom;

use Zend\View\Model\ViewModel;

class CustomViewModel extends ViewModel {

    function __construct($variables = null, $options = null) {
        parent::__construct($variables == null ? null : ["data" => $variables], $options);
        $this->setTerminal(true);
        $this->setTemplate('layout/json');
    }

}
