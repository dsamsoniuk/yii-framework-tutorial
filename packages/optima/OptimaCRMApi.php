<?php

namespace app\components\optima;

use app\components\optima\OptimaApi;

/**
 * Api Optima connection
 */
class OptimaCRMApi extends OptimaApi
{
    public function __construct()
    {
        parent::__construct('url', 'login', 'password');
    }

}
