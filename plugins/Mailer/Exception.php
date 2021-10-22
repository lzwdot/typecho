<?php

namespace TypechoPlugin\Mailer;

/**
 * PHPMailer exception handler
 * @package PHPMailer
 */
class Exception extends \Exception
{
    /**
     * Prettify error message output
     * @return string
     */
    public function errorMessage()
    {
        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
        return $errorMsg;
    }
}