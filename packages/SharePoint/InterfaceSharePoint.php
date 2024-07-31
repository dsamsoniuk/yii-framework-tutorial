<?php

namespace app\components\SharePoint;

use Office365\SharePoint\ClientContext;

interface InterfaceSharePoint {
    public function getClient():ClientContext|SharePointException;
    public function addError(string $mesage);
    public function isError();
}