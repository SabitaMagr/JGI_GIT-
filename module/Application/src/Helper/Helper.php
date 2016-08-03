<?php

namespace Application\Helper;

class Helper
{
    public static function addFlashMessagesToArray($context,$return)
    {
        $flashMessenger = $context->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $return['messages'] = $flashMessenger->getMessages();
        }
        return $return;
    }
}