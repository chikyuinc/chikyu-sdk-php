<?php namespace Chikyu\Sdk;


use Chikyu\Sdk\Error\ApiExecuteException;

class PublicResource extends ApiResource {
    private $apiKey = null;
    private $authKey = null;

    public function __construct($apiKey, $authKey) {
        $this->apiKey = $apiKey;
        $this->authKey = $authKey;
    }

    /**
     * @param $apiPath
     * @param $data
     * @return mixed
     * @throws ApiExecuteException
     */
    function invoke($apiPath, $data) {
        $url = self::buildUrl('public', $apiPath);
        $res = self::sendRequest($url, array('data' => $data),
                                    array('x-api-key'=> $this->apiKey, 'x-auth-key' => $this->authKey));
        return self::handleResponse($res);
    }

}
