<?php

namespace app\components\SharePoint;

class SharePointException extends \Exception {

    public function __construct(string $message = "", int $code = 404, \Throwable $previous = null){
        return parent::__construct($message , $code);
    }
}