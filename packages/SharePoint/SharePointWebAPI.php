<?php

namespace app\components\SharePoint;

/**
 * Strona: xxxxxxx.sharepoint.com/sites/WebAPI
 */
class SharePointWebAPI extends SharePointApi {
    public function __construct(){

        $url            = isset(\Yii::$app->params['apiSharePointSite']) ? \Yii::$app->params['apiSharePointSite'] : '';
        $clientId       = isset(\Yii::$app->params['apiSharePointClientID']) ? \Yii::$app->params['apiSharePointClientID'] : '';
        $clientSecret   = isset(\Yii::$app->params['apiSharePointClientSecret']) ? \Yii::$app->params['apiSharePointClientSecret'] : '';

        return parent::__construct($url, $clientId, $clientSecret);
    }
}
