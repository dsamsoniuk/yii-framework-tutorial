<?php

namespace app\components\optima;

class ApiServiceOrders {

    public OptimaApi $optimaApi;

    /**
     * @param OptimaApi $optimaApi
     */
    public function __construct(OptimaApi $optimaApi)
    {
        $this->optimaApi = $optimaApi;
        $this->optimaApi->setAuthBearer();
    }

    public function getList($params = []): array {
        $this->optimaApi->client->setGetParams($params);
        return $this->optimaApi->sendRequest('/ServiceOrders', 'get');
    }
    /**
     * Pobieranie pojedynczego zlecenia
     * @param integer $id - id z Optimy
     * @return array
     */
    public function getOne($id): array {
        $this->optimaApi->client->setGetParams(['id' => $id]);
        return $this->optimaApi->sendRequest('/ServiceOrders');
    }
    /**
     * Pobierz zlecenie po parametrach
     * @param array $params
     *
     * @return array
     */
    public function getBy(array $params = []): array {
        $this->optimaApi->client->setGetParams($params);
        return $this->optimaApi->sendRequest('/ServiceOrders');
    }
    /**
     * Stworz zlecenie
     * @param array $postParams
     * @return array
     */
    public function addElement(array $postParams): array {
        if (isset($postParams['optimaLoginData'])) {
            unset($postParams['optimaLoginData']);
        }
        $this->optimaApi->client->setPostParams($postParams);
        return $this->optimaApi->sendRequest('/ServiceOrders', 'post');
    }
    /**
     * Aktualizuj zlecenie
     * @param array $params
     * @return array
     */
    public function update(array $params): array {
        $id             = isset($params['id']) ? $params['id'] : null;
        $optimaParams   = $this->getBy(['id' => $id ]);
        if (isset($optimaParams['id'])) {
            $params = array_merge($optimaParams, $params);
        }
        if (isset($params['recipient']['updateCustomerFields'])){
            unset($params['recipient']['updateCustomerFields']);
        }
        if (isset($params['payer']['updateCustomerFields'])){
            unset($params['payer']['updateCustomerFields']);
        }
        if (isset($params['optimaLoginData'])) {
            unset($params['optimaLoginData']);
        }
        $this->optimaApi->client->setPostParams($params);
        return $this->optimaApi->sendRequest('/ServiceOrders', 'put');
    }
}
