<?php

namespace Dipesh79\LaravelPhonePe\Exception;

class PhonePeException extends \Exception
{

    public function __construct($message = "PhonePe Exception", $code = 500, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
