<?php namespace Chikyu\Sdk\Resource;

use Aws\Sts;
use Chikyu\Sdk\Config\Configs;
use Chikyu\Sdk\Error\ApiExecuteException;
use Chikyu\Sdk\OpenResource;
use Chikyu\Sdk\SecureResource;

class Session {
    private $sessionId;
    private $identityId;
    private $apiKey;
    private $credential;
    private $user;

    private function __construct($sessionId, $identityId, $apiKey, $credential, $user) {
        $this->sessionId = $sessionId;
        $this->identityId = $identityId;
        $this->apiKey = $apiKey;
        $this->credential = $credential;
        $this->user = $user;
    }

    /**
     * @param $tokenName
     * @param $loginToken
     * @param $loginSecretToken
     * @return Session
     * @throws ApiExecuteException
     */
    public static function login($tokenName, $loginToken, $loginSecretToken) {
        $resource = new OpenResource();
        $res = $resource->invoke('/session/login', array(
            'token_name' =>$tokenName,
            'login_token' => $loginToken,
            'login_secret_token' => $loginSecretToken
        ));

        $sessionId = $res['session_id'];
        $identityId = $res['cognito_identity_id'];
        $cognitoToken = $res['cognito_token'];
        $user = $res['user'];
        $apiKey = $res['api_key'];

        $sts = Sts\StsClient::factory();

        $token = $sts->assumeRoleWithWebIdentity(array(
            'RoleArn' => Configs::awsRoleArn(),
            'RoleSessionName' => Configs::awsApiGatewayServiceName(),
            'WebIdentityToken' => $cognitoToken
        ));

        $credentials = $token->get('Credentials');

        return new Session($sessionId, $identityId, $apiKey, $credentials, $user);
    }

    /**
     * @param $organ_id
     * @throws ApiExecuteException
     */
    public function changeOrgan($organ_id) {
        $resource = new SecureResource($this);
        $res = $resource->invoke('/session/organ/change', array('target_organ_id' => $organ_id));
        $this->apiKey = $res['api_key'];
    }

    /**
     * @throws ApiExecuteException
     */
    public function logout() {
        $resource = new SecureResource($this);
        $resource->invoke('/session/logout', array());
    }

    /**
     * @return mixed
     */
    public function getSessionId() {
        return $this->sessionId;
    }

    /**
     * @return mixed
     */
    public function getIdentityId() {
        return $this->identityId;
    }

    /**
     * @return mixed
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * @return mixed
     */
    public function getCredential() {
        return $this->credential;
    }

    /**
     * @return mixed
     */
    public function getUser() {
        return $this->user;
    }
}