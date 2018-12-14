<?php namespace Chikyu\Sdk\Resource;

use Aws\Sts\StsClient;
use Chikyu\Sdk\Config\ApiConfig;
use Chikyu\Sdk\Error\ApiExecuteException;
use Chikyu\Sdk\OpenResource;
use Chikyu\Sdk\SecureResource;

class Session {
    private $sessionId;
    private $sessionSecretKey;
    private $identityId;
    private $apiKey;
    private $credential;
    private $user;

    private function __construct($sessionId, $identityId, $apiKey, $credential, $user, $sessionSecretKey=null) {
        $this->sessionId = $sessionId;
        $this->identityId = $identityId;
        $this->apiKey = $apiKey;
        $this->credential = $credential;
        $this->user = $user;
        $this->sessionSecretKey = $sessionSecretKey;
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
        return self::fromData($res);
    }

    public static function fromData($data) {
        $sessionId = $data['session_id'];
        $identityId = $data['cognito_identity_id'];
        $cognitoToken = $data['cognito_token'];
        $user = $data['user'];
        $apiKey = $data['api_key'];

        $sessionSecretKey = null;
        if (array_key_exists('session_secret_key', $data)) {
            $sessionSecretKey = $data['session_secret_key'];
        }

        $sts = new StsClient(array(
            'version' => 'latest',
            'region' => ApiConfig::awsRegion(),
            'credentials' => false
        ));

        $token = $sts->assumeRoleWithWebIdentity(array(
            'RoleArn' => ApiConfig::awsRoleArn(),
            'RoleSessionName' => ApiConfig::awsApiGatewayServiceName(),
            'WebIdentityToken' => $cognitoToken,
            "DurationSeconds" => 43200
        ));

        $credentials = $token->get('Credentials');

        return new Session($sessionId, $identityId, $apiKey, $credentials, $user, $sessionSecretKey);
    }

    /**
     * @param $organ_id
     * @throws ApiExecuteException
     */
    public function changeOrgan($organ_id) {
        $resource = new SecureResource($this);
        $res = $resource->invoke('/session/organ/change', array('target_organ_id' => $organ_id));
        $this->apiKey = $res['api_key'];
        $this->user = $res['user'];
    }

    /**
     * @throws ApiExecuteException
     */
    public function logout() {
        $resource = new SecureResource($this);
        $resource->invoke('/session/logout', array());
        $this->sessionId = null;
        $this->sessionSecretKey = null;
        $this->user = null;
        $this->credential = null;
        $this->apiKey = null;
        $this->identityId = null;
    }

    public function toArray($withUser=true) {
        if ($withUser) {
            return [
                'sessionId' => $this->sessionId,
                'sessionSecretKey' => $this->sessionSecretKey,
                'identityId' => $this->identityId,
                'apiKey' => $this->apiKey,
                'credentials' => $this->credential,
                'user' => $this->user
            ];
        } else {
            return [
                'sessionId' => $this->sessionId,
                'sessionSecretKey' => $this->sessionSecretKey,
                'identityId' => $this->identityId,
                'apiKey' => $this->apiKey,
                'credentials' => $this->credential,
                'user' => ['user_id' => $this->user['user_id']]
            ];
        }
    }

    public static function fromStr($jsonString) {
        $item = json_decode($jsonString, true);
        return self::fromArray($item);
    }

    public static function fromArray(array $item) {
        return new Session(
            $item['sessionId'], $item['identityId'], $item['apiKey'], $item['credentials'], $item['user']);
    }

    public function __toString()
    {
        return json_encode($this->toArray());
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