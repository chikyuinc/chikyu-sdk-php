<?php namespace Chikyu\Sdk\Config;


class ApiConfig {
    const AWS_REGION = 'ap-northeast-1';
    const AWS_API_GW_SERVICE_NAME = 'execute-api';
    const AWS_ROLE_ARN = 'arn:aws:iam::171608821407:role/Cognito_Chikyu_Normal_Id_PoolAuth_Role';
    const AWS_ROLE_DEV_ARN = 'arn:aws:iam::527083274078:role/Cognito_ChikyuDevLocalAuth_Role';
    const AWS_ROLE_DEV2_ARN = 'arn:aws:iam::527083274078:role/Cognito_Chikyu_Normal_Id_Pool_devAuth_Role';
    const AWS_ROLE_PROD_ARN = 'arn:aws:iam::171608821407:role/Cognito_chikyu_PROD_idpoolAuth_Role';

    static private $MODE = 'prod';

    const HOSTS = [
        'local' => 'localhost:9090',
        'docker' => 'dev-python:9090',
        'devdc' => 'gateway.chikyu.mobi',
        'dev01' => 'gateway.chikyu.mobi',
        'dev02' => 'gateway.chikyu.mobi',
        'dev03' => 'gateway-dev.chikyu.mobi',
        'hotfix01' => 'gateway.chikyu.mobi',
        'prod' => 'endpoint.chikyu.net'
    ];

    const PROTOCOLS = [
        'local' => 'http',
        'docker' => 'http',
        'devdc' => 'https',
        'dev01' => 'https',
        'dev02' => 'https',
        'dev03' => 'https',
        'hotfix01' => 'https',
        'prod' => 'https'
    ];

    const ENV_NAMES = [
        'local' => '',
        'docker' => '',
        'devdc' => 'dev',
        'dev01' => 'dev01',
        'dev02' => 'dev02',
        'dev03' => 'dev03',
        'hotfix01' => 'hotfix01',
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
        }else if (self::$MODE == 'dev03'){
            return self::AWS_ROLE_DEV2_ARN;
        } else {
            return self::AWS_ROLE_ARN;
        }
    }

    static function awsApiGatewayServiceName() {
        return self::AWS_API_GW_SERVICE_NAME;
    }

    static function host() {
        return self::HOSTS[self::mode()];
    }

    static function protocol() {
        return self::PROTOCOLS[self::mode()];
    }

    static function envName() {
        return self::ENV_NAMES[self::mode()];
    }

    static function mode() {
        return self::$MODE;
    }

    static function setMode($mode) {
        self::$MODE = $mode;
    }

}
