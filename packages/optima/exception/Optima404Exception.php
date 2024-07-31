<?php

namespace app\components\optima\exception;

use Exception;

class Optima404Exception extends Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct("Not found.".$message, 404);
    }
}
