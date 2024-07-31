<?php

namespace app\components\optima\exception;

use Exception;

class OptimaRequestTimeoutException extends Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct("Optima request timeout..". $message, 408);
    }
}
