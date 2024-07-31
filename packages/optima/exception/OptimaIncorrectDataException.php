<?php

namespace app\components\optima\exception;

use Exception;

class OptimaIncorrectDataException extends Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct("Error due to incorrect data sent by the user..". $message, 400);
    }
}
