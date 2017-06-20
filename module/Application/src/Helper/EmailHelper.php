<?php

namespace Application\Helper;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class EmailHelper {

    public static function getSmtpTransport(): Smtp {
        $transport = new Smtp();
        $options = new SmtpOptions([
            'host' => 'duster.websitewelcome.com',
            'port' => 587,
            'connection_class' => 'login',
            'connection_config' => [
                'username' => 'ukesh.gaiju@itnepal.com',
                'password' => 'ukesh@123',
                'ssl' => 'tls',
            ],
        ]);
        $transport->setOptions($options);
        return $transport;
    }

    public static function sendEmail(Message $mail) {
        $transport = self::getSmtpTransport();
        $transport->send($mail);
        return true;
    }

}
