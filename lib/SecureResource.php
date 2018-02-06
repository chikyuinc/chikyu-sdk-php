<?php namespace ChikyuSdk;

use ChikyuSdk\Config\Configs;
use ChikyuSdk\Error\ApiExecuteException;
use ChikyuSdk\Helper\ApiRequestSigner;

class SecureResource extends ApiResource {
    private $session = null;

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
