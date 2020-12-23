<?php namespace Chikyu\Sdk;


use Chikyu\Sdk\Config\ApiConfig;
use Chikyu\Sdk\Error\ApiExecuteException;
use Chikyu\Sdk\Helper\ApiRequestSigner;
use Chikyu\Sdk\Resource\Session;
use Chikyu\App\Utils;

class SecureResource extends ApiResource {
    private $session = null;

    /**
     * SecureResource constructor.
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
        $signer = new ApiRequestSigner(
                        $this->session->getCredential(), $this->session->getApiKey());

        $params = array(
            'session_id' => $this->session->getSessionId(),
            'data' => $data
        );

        if (ApiConfig::mode() == 'local' || ApiConfig::mode() == 'docker') {
            $params['identity_id'] = $this->session->getIdentityId();
        }

        $json = json_encode($params);
        Utils::log("json: $json");
        $path = self::buildUrl('secure', $apiPath, false);
        Utils::log("path: $path");
        $headers = $signer->sign($path, $json);

        Utils::log("headers: $headers");
        Utils::log("apiPath: $apiPath");
        Utils::log("params: $params");

        $res = self::sendRequest(self::buildUrl('secure', $apiPath), $params, $headers);
        Utils::log("res: $res");

        return self::handleResponse($res);
    }
}
