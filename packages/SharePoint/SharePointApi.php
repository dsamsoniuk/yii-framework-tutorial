<?php

namespace app\components\SharePoint;

use app\modules\log\models\Log;
use Office365\Runtime\Auth\ClientCredential;
use Office365\SharePoint\ClientContext;


abstract class SharePointApi implements InterfaceSharePoint {

    /** @var ClientContext $client */
    private $client;
    public $errors;

    public function __construct(string $url, string $clientId, string $clientSecret){

        $this->errors   = [];

        if ($clientId == '' || $clientSecret == '') {
            $this->addError('Brak paremetrów dla API SharePoint (clientId, clientSecret)');            
        } else {
            $credentials    = new ClientCredential($clientId, $clientSecret);
            $this->client   = (new ClientContext($url))->withCredentials($credentials);
        }
    }
    /**
     * @var ClientContext
     */
    public function getClient(): ClientContext|SharePointException {
        if ($this->client == null) {
            throw new SharePointException('Klient API SharePoint nie istnieje');
        }
        return $this->client;
    }
    /**
     * Dodaj błąd
     */
    public function addError(string $message): void{
        $this->errors[] = $message;

        $log = new Log();
        $log->message   = $message;
        $log->log_time  = time();

        if (\Yii::$app->getRequest() instanceof \yii\web\Request) {
            $log->category  = 'SharePoint API - Web';
        } else if (\Yii::$app->getRequest() instanceof \yii\console\Request) {
            $log->category  = 'SharePoint API - Console';
        } else {
            $log->category  = 'SharePoint API';
        }

        if (YII_DEBUG && \Yii::$app->getRequest() instanceof \yii\web\Request && \Yii::$app->user) {
            \Yii::$app->session->addFlash('danger', 'SharePoint API: '. $message);
            // $log->created_by = \Yii::$app->user->id;
        }

        $log->save();
    }
    /**
     * Czy wystapiły błędy
     */
    public function isError(): bool{
        return count($this->errors) > 0;
    }
}