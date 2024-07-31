<?php

namespace app\components\optima\exception;

use Exception;

class OptimaExecutingQueryException extends Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct("Unexpected problems while executing the query.".$message, 500);
    }
}
