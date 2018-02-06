<?php namespace ChikyuSdk\Helper;

use ChikyuSdk\Config\Configs;


/**
 * Class ApiRequestSigner
 * AWS 署名バージョン4 署名プロセスを一部実装。
 * @package Chikyu
 */
class ApiRequestSigner {
    private $credential = null;
    public $apiKey = null;

    private static $POST = "POST";
    private static $AWS4_HMAC_SHA256 = "AWS4-HMAC-SHA256";
    private static $AWS4_REQUEST = "aws4_request";


    public function __construct($credential, $apiKey) {
        $this->credential = $credential;
        $this->apiKey = $apiKey;
    }

    public function sign($path, $payload) {
        $time = $this->getTimeStamp();
        $date = $this->getDate();

        //この部分のhttpヘッダ名は小文字である必要がある。
        $headers = array();
        $headers['content-type'] = 'application/json';
        $headers['host'] = Configs::host();
        $headers['x-amz-date'] = $time;
        $headers['x-amz-security-token'] = $this->credential['SessionToken'];
        $headers['x-api-key'] = $this->apiKey;

        $signedHeaders = $this->getSignedHeader($headers);
        $canonicalUrl = $this->getCanonicalUrl($path, $payload, $signedHeaders, $headers);

        $stringToSign = $this->getStringToSign($canonicalUrl, $time, $date);

        $signature = $this->getSignature($stringToSign, $date);

        $headers['Authorization'] = $this->buildAuthorizationHeader($signedHeaders, $signature, $date);

        return $headers;
    }

    private function getSignedHeader($headers) {
        $res = "";
        $i = 0;
        foreach (array_keys($headers) as $k) {
            if ($i++ > 0) {
                $res .= ';';
            }
            $res .= $k;
        }
        return $res;
    }

    private function getCanonicalUrl($path, $payload, $signedHeaders, $headers) {
        $res = self::$POST . "\n";
        $res .= $path . "\n\n";

        foreach ($headers as $k => $v) {
            $res .= "{$k}:${v}\n";
        }
        $res .= "\n";

        $res .= $signedHeaders . "\n";
        $res .= $this->getMessageDigest($payload);

        return $res;
    }

    private function buildAuthorizationHeader($signedHeaders, $signature, $currentTime) {
        $a = self::$AWS4_HMAC_SHA256;
        $b = $this->credential['AccessKeyId'] . '/' . $this->getServiceDescription($currentTime);
        return "{$a} Credential={$b},SignedHeaders={$signedHeaders},Signature={$signature}";
    }

    private function getServiceDescription($currentDate) {
        $a = Configs::awsRegion();
        $b = Configs::awsApiGatewayServiceName();
        $c = self::$AWS4_REQUEST;
        return "{$currentDate}/{$a}/{$b}/{$c}";
    }

    private function getStringToSign($canonicalUrl, $timeStamp, $currentDate) {
        $a = self::$AWS4_HMAC_SHA256;
        $b = $timeStamp;
        $c = $this->getServiceDescription($currentDate);
        $d = $this->getMessageDigest($canonicalUrl);
        return "{$a}\n{$b}\n{$c}\n{$d}";
    }

    private function getSignature($stringToSign, $currentDate) {
        $key = $this->getSignatureKey($this->credential['SecretAccessKey'], $currentDate, true);
        return $this->getHmacSha256($key, $stringToSign);
    }

    private function getSignatureKey($key, $date, $is_raw=false) {
        $secret = "AWS4{$key}";
        $keyDate = $this->getHmacSha256($secret, $date, true);
        $keyRegion = $this->getHmacSha256($keyDate, Configs::awsRegion(), true);
        $keyService = $this->getHmacSha256($keyRegion, Configs::awsApiGatewayServiceName(), true);
        return $this->getHmacSha256($keyService, self::$AWS4_REQUEST, $is_raw);
    }

    private function getTimeStamp() {
        return gmdate("Ymd\THis\Z");
    }

    private function getDate() {
        return gmdate('Ymd');
    }

    private function getMessageDigest($str) {
        return hash('sha256', $str);
    }

    private function getHmacSha256($key, $data, $is_raw=false) {
        return hash_hmac('sha256', $data, $key, $is_raw);
    }
}