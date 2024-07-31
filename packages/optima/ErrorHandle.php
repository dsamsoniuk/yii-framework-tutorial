<?php

namespace app\components\optima;

use app\components\File;
use app\modules\log\models\Log;
use Yii;
use yii\helpers\VarDumper;

class ErrorHandle
{
    
    public function logMessage($msg, $category = 'message-from-api-optima')
    {
        if(!is_string($msg))
        {
            $error = VarDumper::dumpAsString($msg);
        }
        Yii::error($msg, $category);
    }
    
    public function logError($error, $category = 'error-from-api-optima')
    {
        if(!is_string($error))
        {
            $error = VarDumper::dumpAsString($error);
        }
        Yii::error($error, $category);

        $formatter  = Yii::$app->formatter;
        $content    = "\n::: " . $formatter->asDatetime('now') . " ::: " . $error ;
        $dir        = __DIR__.'/../../runtime';

        if (is_dir($dir) && is_writable($dir)) {
            File::write($dir.'/optima_logs.txt', $content, FILE_APPEND);
        } else {
            Log::addLog('Błąd przy zapisie do katalogu '.$dir, 1, 'error-dir');
        }
    }
}

