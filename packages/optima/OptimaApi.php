<?php

namespace app\components\optima;

use app\components\Curl;
use app\components\optima\exception\Optima404Exception;
use app\components\optima\exception\OptimaExecutingQueryException;
use app\components\optima\exception\OptimaIncorrectDataException;
use app\components\optima\exception\OptimaRequestTimeoutException;
use app\models\OptimaToken;
use app\models\User;
use app\modules\admin\models\Configurator\Device;
use app\modules\log\models\Log;
use app\modules\log\models\LogOptima;
use yii\helpers\Json;
use Exception;
use Yii;
use app\helpers\MailerHelper;
use yii\helpers\Url;

/**
 * Api Optima connection
 * @param string $responseStatus
 * @param string $responseMessage
 */
abstract class OptimaApi
{
    /** Ilosc prob powtorzen zapytania jezeli nie powiedzie sie */
    const COUNT_REPEAT_REQUEST = 1;

    /**
     * @var Curl
     */
    public Curl $client;
    /**
     * @var ErrorHandle
     */
    public ErrorHandle $errorHandle;

    private string $url;
    private string $userName;
    private string $userPassword;
    private int $countRequestLeft = 0;
    public int $responseStatus = 0;
    public $responseMessage = '';

    /**
     * @param string $url
     * @param string $userName
     * @param string $userPassword
     */
    public function __construct(string $url, string $userName, string $userPassword)
    {
        $this->errorHandle  = new ErrorHandle();
        $this->client       = new Curl();

        $this->countRequestLeft = self::COUNT_REPEAT_REQUEST;
        $this->url              = $url;
        $this->userName         = $userName;
        $this->userPassword     = $userPassword;
    }

    /**
     * Bearer authorization
     * @return void
     */
    public function setAuthBearer(): void
    {
        $this->client->setOption(CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $this->getToken()
        ));
    }

    /**
     * @param string $url
     * @param string $method
     *
     * @return array
     */
    public function sendRequest(string $url, string $method = 'get'): array
    {

        try {
            $response   = $this->client->{$method}($this->url . $url);
            $code       = $this->client->responseCode ? (string) $this->client->responseCode : null;
            $data       = Json::decode($response ?: '') ?: [];

            $this->addOptimaLog($response, $code, $url, $method);

            $this->responseStatus   = (int) $code;
            $this->responseMessage  = $response;

            if ($code === '400') {
                throw new OptimaIncorrectDataException($response);
            } else if ($code === '404') {
                throw new Optima404Exception($response);
            } else if ($code === '500') {
                throw new OptimaExecutingQueryException($response);
            } else if ($code === 'timeout') {
                throw new OptimaRequestTimeoutException($response);
            }
        } catch (Exception $e) {
            $this->errorHandle->logError($e->getMessage());
            $data = [];
            if ($this->countRequestLeft > 0) {
                $this->countRequestLeft--;
                return $this->sendRequest($url, $method);
            }
            if (YII_DEBUG && Yii::$app->getRequest() instanceof yii\web\Request) {
                Yii::$app->session->addFlash('danger', $e->getMessage());
            }
        }
        $this->countRequestLeft = self::COUNT_REPEAT_REQUEST;

        return $data;
    }

    /**
     * Generuj nowy token z Optimy
     * @return string
     */
    public function getToken(): string
    {

        $class = explode('\\', get_class($this));
        $className = end($class);

        $optimaToken = OptimaToken::find()
            ->where('expire > ' . time())
            ->andWhere(['class' => $className])
            ->one();

        if ($optimaToken) {
            return $optimaToken->token;
        }

        $this->client->setOption(CURLOPT_POSTFIELDS, http_build_query([
            'username' => $this->userName,
            'password' => $this->userPassword,
            'grant_type' => 'password'
        ]));

        $response = $this->sendRequest('/Token', 'post');

        if (empty($response)) {
            return '';
        }

        $token = new OptimaToken();
        $token->token = $response['access_token'];
        $token->expire = time() + $response['expires_in'];
        $token->class = $className;
        $token->save();

        return $response['access_token'];
    }
    /**
     * @return array
     */
    public static function getLastLogs(): array
    {
        return Log::getLastLogs('error-from-api-optima');
    }
    /**
     * Zwroc szczegoly źądania Optimy
     * @param mixed $method
     * @param mixed $code
     * @param mixed $url
     *
     * @return string
     */
    public function getRequestDataToString($method, $code, $url): string
    {
        $device             = null;
        $routesForDevice    = [
            "admin/project-device/update" => "id",
        ];

        $params = [
            "REQUEST_API: " . $method,
            "STATUS_API:" . $code,
            "URL_API:" . $url,
            "POST_API:" . $this->client->optionsAsString(),
            "GET_API:" . $this->client->getParamsAsString(),
        ];

        if (isset(Yii::$app->session) && isset(Yii::$app->request)) {
            /** @var User $user */
            $user               = Yii::$app->user->identity;
            $pathInfo           = Yii::$app->request->pathInfo ?? null;
            if ($pathInfo && isset($routesForDevice[$pathInfo])) {
                $device = Device::findOne(['id' => Yii::$app->request->get($routesForDevice[$pathInfo])]);
            } else if (Yii::$app->request->get('device_id')) {
                $device = Device::findOne(['id' => Yii::$app->request->get('device_id')]);
            }
            $params = array_merge($params, [
                "PAGE_POST:" . json_encode(Yii::$app->request->post()),
                "PAGE_URL:" . Yii::$app->request->url,
                "USER_ID:" . $user->id,
                "USER_NAME:" . $user->getFullName(),
                "USER_ROLE:" . $user->role,
                "DEVICE_ID:" . ($device ? $device->id : ''),
                "PROJECT:" . ($device ? $device->project->optima_no : ''),
            ]);
        }

        return implode("\n", $params);
    }

    /**
     * @param string $requestData
     * @param string $responseData
     * @param string|null $statusCode
     * @param string|null $urlApi
     * @param string|null $method
     *
     * @return [type]
     */
    private function addOptimaLog(string $responseData, ?string $statusCode = null, ?string $urlApi = null, ?string $method = null)
    {
        $log = new LogOptima();
        $log->response  = $responseData;
        $log->status    = $statusCode;
        $log->url_api   = $urlApi;
        $log->method    = $method;

        if (Yii::$app->getRequest() instanceof yii\web\Request) {
            $log->request       = $this->getRequestDataToString($method, $statusCode, $urlApi);
            $log->url           = Yii::$app->request->url;
            $log->created_by    = Yii::$app->user->id;
        } else if (Yii::$app->getRequest() instanceof yii\console\Request) {
            $log->url           = 'console:'. json_encode(Yii::$app->getRequest()->getParams());
        }

        $log->save();
        $this->sendLogByEmail($log);
    }

    private function sendLogByEmail(?LogOptima $log): void
    {
        $notifyPaths = ['/ServiceOrders', '/Documents', '/DocumentItems'];
        $notifyStatuses = [400, 401, 404, 500];

        if (null !== $log && $log->method === 'post' && in_array($log->url_api, $notifyPaths) && in_array($log->status, $notifyStatuses)) {

            if (Yii::$app->getRequest() instanceof yii\web\Request) {
                $content = Yii::$app->view->renderFile('@new_mail/message/log-optima-notification.php', [
                    'model' => $log,
                ]);
            } else {
                $content = 'log_optima_id: '.$log->id."<br>".$log->response;
            }

            MailerHelper::send(Yii::$app->params['adminEmail'], 'Błąd Optima API: ' . $log->url_api, $content);
        }
    }
}
