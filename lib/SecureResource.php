<?php namespace Chikyu\Sdk;


use Chikyu\Sdk\Config\Configs;
use Chikyu\Sdk\Error\ApiExecuteException;
use Chikyu\Sdk\Helper\ApiRequestSigner;
use Chikyu\Sdk\Resource\Session;

class SecureResource extends ApiResource {
    private $session = null;

    /**
     * SecureResource constructor.
     * @param Session
     */
    public function __construct($session) {
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

        if (Configs::mode() == 'local') {
            $params['identity_id'] = $this->session->getIdentityId();
        }

        $json = json_encode($params);
        $path = self::buildUrl('secure', $apiPath, false);
        $headers = $signer->sign($path, $json);

        $res = self::sendRequest(self::buildUrl('secure', $apiPath), $params, $headers);

        return self::handleResponse($res);
    }
}
