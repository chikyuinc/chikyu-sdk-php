<?php namespace Chikyu\Sdk\Config;

use Chikyu\Sdk\Log\ApiLogger;

class ApiConfig {
    const AWS_REGION = 'ap-northeast-1';
    const AWS_API_GW_SERVICE_NAME = 'execute-api';
    const AWS_ROLE_ARN = 'arn:aws:iam::171608821407:role/Cognito_Chikyu_Normal_Id_PoolAuth_Role';
    const AWS_ROLE_DEV_ARN = 'arn:aws:iam::527083274078:role/Cognito_ChikyuDevLocalAuth_Role';
    const AWS_ROLE_PROD_ARN = 'arn:aws:iam::171608821407:role/Cognito_chikyu_PROD_idpoolAuth_Role';

    static private $MODE = 'prod';

    static private $CLIENT_IP = null;

    const HOSTS = [
        'local' => 'localhost:9090',
        'docker' => 'dev-python:9090',
        'prod' => 'endpoint.chikyu.net'
    ];

    const PROTOCOLS = [
        'local' => 'http',
        'docker' => 'http',
        'prod' => 'https'
    ];

    const ENV_NAMES = [
        'local' => '',
        'docker' => '',
        'devdc' => 'dev',
        'prod' => ''
    ];

    static function awsRegion() {
        return self::AWS_REGION;
    }

    static function awsRoleArn() {
        if (self::$MODE == 'prod'){
            return self::AWS_ROLE_PROD_ARN;
        } else if (self::$MODE == 'local' || self::$MODE == 'docker') {
            return self::AWS_ROLE_DEV_ARN;
        } else {
            return self::AWS_ROLE_ARN;
        }
    }

    static function awsApiGatewayServiceName() {
        return self::AWS_API_GW_SERVICE_NAME;
    }

    static function host() {
        // PHP5.6なのでNull合体演算子は使用不可
        return !is_null(self::HOSTS[self::mode()]) ? self::HOSTS[self::mode()] : 'gateway.chikyu.mobi';
    }

    static function protocol() {
        // PHP5.6なのでNull合体演算子は使用不可
        return !is_null(self::PROTOCOLS[self::mode()]) ? self::PROTOCOLS[self::mode()] : 'https';
    }

    static function envName() {
        // PHP5.6なのでNull合体演算子は使用不可
        return !is_null(self::ENV_NAMES[self::mode()]) ? self::ENV_NAMES[self::mode()] : self::mode();
    }

    static function mode() {
        return self::$MODE;
    }

    static function setMode($mode) {
        self::$MODE = $mode;
    }

    /**
     * リクエストの送信元となるエンドユーザーの IP アドレスを返す。未設定の場合は null。
     */
    static function clientIp() {
        return self::$CLIENT_IP;
    }

    /**
     * エンドユーザーの IP アドレスを設定する。設定すると X-Forwarded-For として送信され、API 側の監査ログに記録される。
     * 信頼できる IP を 1 リクエストにつき 1 回設定すること。null で解除する。
     * IP アドレスとして不正な値（空文字など）は例外にせず、警告ログを出して未設定（null）として扱う。
     *
     * @param string|null $clientIp IPv4 または IPv6 アドレス
     */
    static function setClientIp($clientIp) {
        if (!is_null($clientIp) && (!is_string($clientIp) || filter_var($clientIp, FILTER_VALIDATE_IP) === false)) {
            try {
                ApiLogger::warn('ApiConfig: the client IP is not an IP address, X-Forwarded-For will not be sent');
            } catch (\Throwable $e) {
                // ログ出力の失敗で呼び出し側の処理を止めない
            }
            $clientIp = null;
        }
        self::$CLIENT_IP = $clientIp;
    }

}
