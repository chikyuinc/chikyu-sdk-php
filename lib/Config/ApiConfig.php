<?php namespace Chikyu\Sdk\Config;


class ApiConfig {
    const AWS_REGION = 'ap-northeast-1';
    const AWS_API_GW_SERVICE_NAME = 'execute-api';
    const AWS_ROLE_ARN = 'arn:aws:iam::171608821407:role/Cognito_Chikyu_Normal_Id_PoolAuth_Role';
    const AWS_ROLE_DEV_ARN = 'arn:aws:iam::527083274078:role/Cognito_ChikyuDevLocalAuth_Role';
    const AWS_ROLE_PROD_ARN = 'arn:aws:iam::171608821407:role/Cognito_chikyu_PROD_idpoolAuth_Role';

    static private $MODE = 'prod';

    const HOSTS = [
        'local' => 'localhost:9090',
        'docker' => 'dev-python:9090',
        'prod' => 'endpoint.chikyu.net'
    ];

    const PROTOCOLS = [
        'local' => 'http',
        'docker' => 'http',
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
        if(isset(self::HOSTS[self::mode()])) {
            return self::HOSTS[self::mode()];
        }
        return 'gateway.chikyu.mobi';
    }

    static function protocol() {
        // PHP5.6なのでNull合体演算子は使用不可
        if(isset(self::PROTOCOLS[self::mode()])) {
            return self::PROTOCOLS[self::mode()];
        }
        return 'https';
    }

    static function envName() {
        // PHP5.6なのでNull合体演算子は使用不可
        if(isset(self::ENV_NAMES[self::mode()])) {
            return self::ENV_NAMES[self::mode()];
        }
        return self::mode();
    }

    static function mode() {
        return self::$MODE;
    }

    static function setMode($mode) {
        self::$MODE = $mode;
    }

}
