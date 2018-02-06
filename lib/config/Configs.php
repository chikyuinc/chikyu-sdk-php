<?php namespace ChikyuSdk\Config;


class Configs {
    const AWS_REGION = 'ap-northeast-1';
    const AWS_ROLE_ARN = 'arn:aws:iam::171608821407:role/Cognito_Chikyu_Normal_Id_PoolAuth_Role';
    const AWS_API_GW_SERVICE_NAME = 'execute-api';

    static function awsRegion() {
        return self::AWS_REGION;
    }

    static function awsRoleArn() {
        return self::AWS_ROLE_ARN;
    }

    static function awsApiGatewayServiceName() {
        return self::AWS_API_GW_SERVICE_NAME;
    }

    static function host() {
        if (self::mode() == 'local') {
            return 'localhost:9090';
        } elseif (self::mode() == 'dev') {
            return 'gateway.chikyu.mobi';
        }
        return '';
    }

    static function protocol() {
        if (self::mode() == 'local') {
            return 'http';
        } elseif (self::mode() == 'dev') {
            return 'https';
        }
        return '';
    }

    static function envName() {
        if (self::mode() == 'local') {
            return 'local';
        } elseif (self::mode() == 'dev') {
            return 'dev';
        }
        return '';
    }

    static function mode() {
        return 'dev';
    }
}
