<?php

namespace app\components\optima;

class ApiServiceOrdersAssociate {

    public OptimaApi $optimaApi;

    /**
     * @param OptimaApi $optimaApi
     */
    public function __construct(OptimaApi $optimaApi)
    {
        $this->optimaApi = $optimaApi;
        $this->optimaApi->setAuthBearer();
    }

    /**
     * @param array $params
     * Example
     * 'serviceOrderId' => $service->optima_id,
     * 'documentToAssociateType' => 308, // Typ dokumentu, 308 - REW, 309 - ZD	
     * 'documentToAssociateId' => $device_document->optima_id // Numer ID dokumentu, który będzie wiązany	
     * @return array
     */
    public function add(array $params): array {
        $this->optimaApi->client->setPostParams($params);
        return $this->optimaApi->sendRequest('/ServiceOrdersAssociate2', 'post');
    }
}
