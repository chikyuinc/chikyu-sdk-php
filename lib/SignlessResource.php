<?php namespace Chikyu\Sdk;


use Chikyu\Sdk\Config\ApiConfig;
use Chikyu\Sdk\Error\ApiExecuteException;
use Chikyu\Sdk\Helper\ApiRequestSigner;
use Chikyu\Sdk\Resource\Session;

class SignlessResource extends ApiResource {
    private $session = null;

    /**
     * SignlessResource constructor.
     * @param Session
     */
    public function __construct(Session $session) {
        $this->session = $session;
    }

    /**
     * @param $apiPath
     * @param $data
     * @return mixed
     * @throws ApiExecuteException
     */
    function invoke($apiPath, $data) {
        $salt = gmdate("Ymd\THis\Z");
        $params = array(
            'session_id' => $this->session->getSessionId(),
            'identity_id' => $this->session->getIdentityId(),
            'salt' => $salt,
            'data' => $data
        );

        $json = json_encode($params);
        $apiKey = $this->session->getApiKey();
        $path = self::buildUrl('signless', $apiPath, false);

        $secret = $this->session->getSessionSecretKey();
        $authText = "${salt}&${json}&${secret}";
        $authKey  =hash('sha256', $authText);

        $headers = array(
            'content-type: application/json',
            "x-api-key: {$apiKey}",
            "x-auth-key: {$authKey}",
        );

        $res = self::sendRequest(self::buildUrl('signless', $path), $params, $headers);

        return self::handleResponse($res);
    }
}
