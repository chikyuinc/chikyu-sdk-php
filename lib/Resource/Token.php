<?php namespace Chikyu\Sdk\Resource;

use Chikyu\Sdk\Error\ApiExecuteException;
use Chikyu\Sdk\OpenResource;
use Chikyu\Sdk\SecureResource;

class Token {
    /**
     * @param $token_name
     * @param $email
     * @param $password
     * @param int $duration
     * @return mixed
     * @throws ApiExecuteException
     */
    public static function create($token_name, $email, $password, $duration=86400) {
        $resource = new OpenResource();
        return $resource->invoke('/session/token/create', [
            'token_name' => $token_name,
            'email' => $email,
            'password' => $password,
            'duration' => $duration
        ]);
    }

    /**
     * @param $token_name
     * @param $login_token
     * @param $login_secret_token
     * @param int $duration
     * @return mixed
     * @throws ApiExecuteException
     */
    public static function renew($token_name, $login_token, $login_secret_token, $duration=86400) {
        $resource = new OpenResource();
        return $resource->invoke('/session/token/renew', [
            'token_name' => $token_name,
            'login_token' => $login_token,
            'login_secret_token' => $login_secret_token,
            'duration' => $duration
        ]);
    }

    /**
     * @param $token_name
     * @param $login_token
     * @param $login_secret_token
     * @param $session
     * @return mixed
     * @throws ApiExecuteException
     */
    public static function revoke($token_name, $login_token, $login_secret_token, $session) {
        $resource = new SecureResource($session);
        return $resource->invoke('/session/token/revoke', [
            'token_name' => $token_name,
            'login_token' => $login_token,
            'login_secret_token' => $login_secret_token
        ]);
    }
}