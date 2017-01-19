<?php

namespace Notification\Model;

class NotificationModel {

    public $fromId;
    public $toId;
    public $fromEmail;
    public $toEmail;
    public $fromName;
    public $toName;
    public $fromGender;
    public $toGender;
    public $fromMaritualStatus;
    public $toMaritualStatus;
    public $fromHonorific;
    public $toHonorific;
    public $route;

    public function getObjectAttrs(): array {
        return array_keys(get_object_vars($this));
    }

    public function processString(string $input) {
        $variables = array_keys(get_object_vars($this));
        $output = $input;
        foreach ($variables as $variable) {
            $output = $this->convertVariableToValue($output, $variable);
        }
        return $output;
    }

    private function convertVariableToValue($message, $variable) {
        if (strpos($message, $this->wrapWithLargeBracket($variable)) !== false) {
            $processedVariable = $this->{$variable};
            if (is_string($processedVariable)) {
                return str_replace($this->wrapWithLargeBracket($variable), "'" . $processedVariable . "'", $message);
            } else {
                return str_replace($this->wrapWithLargeBracket($variable), $processedVariable, $message);
            }
        } else {
            return $message;
        }
    }

    private function wrapWithLargeBracket($input) {
        return "[" . $input . "]";
    }

}
