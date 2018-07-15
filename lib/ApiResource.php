<?php namespace Chikyu\Sdk;

use Chikyu\Sdk\Config\ApiConfig;
use Chikyu\Sdk\Error\ApiExecuteException;
use Chikyu\Sdk\Log\ApiLogger;

abstract class ApiResource {
    static function buildUrl($apiClass, $apiPath, $withHost=true) {
        $p = ApiConfig::protocol();
        $h = ApiConfig::host();
        $e = ApiConfig::envName();

        if (strpos($apiPath, '/') === 0) {
            $apiPath = substr($apiPath, 1);
        }

        if ($withHost) {
            if ($e) {
                $e = "/{$e}";
            }

            if ($e == '/prod') {
                return "{$p}://{$h}/v2/{$apiClass}/{$apiPath}";
            } else {
                return "{$p}://{$h}{$e}/api/v2/{$apiClass}/{$apiPath}";
            }
        } else {
            if ($e == 'prod') {
                return "/v2/{$apiClass}/{$apiPath}";
            } else {
                return "/{$e}/api/v2/{$apiClass}/{$apiPath}";
            }
        }
    }

    static function sendRequest($url, $data, $headers) {
        $header_list = array();

        $contentTypeExists = false;
        foreach ($headers as $k => $v) {
            $header_list[] = "{$k}: {$v}";
            if (strtolower($k) == 'content-type') {
                $contentTypeExists = true;
            }
        }

        if (!$contentTypeExists) {
            $header_list[] = 'content-type: application/json';
        }

        ApiLogger::debug("******** REQUEST ********");
        ApiLogger::debug($header_list);
        ApiLogger::debug($url . "\n");
        ApiLogger::debug("*************************");
        $result = file_get_contents($url, false, stream_context_create(array( 'http' =>
            array(
                'method' => 'POST',
                'header' => implode(PHP_EOL, $header_list),
                'content' => json_encode($data),
                'ignore_errors' => false
            )
        )));

        ApiLogger::debug("******** RESPONSE ********");
        ApiLogger::debug($http_response_header);
        ApiLogger::debug($result);
        ApiLogger::debug("*************************");

        return $result;
    }

    /**
     * @param $response
     * @return mixed
     * @throws ApiExecuteException
     */
    static function handleResponse($response) {
        if (!$response) {
            throw new ApiExecuteException("HTTPリクエストの送信に失敗しました");
        }
        $map = json_decode($response, true);

        if ($map['has_error']) {
            throw new ApiExecuteException("APIの呼び出しに失敗しました: " . $map['message']);
        }

        if (array_key_exists('data', $map)) {
            return $map['data'];
        }
    }

    abstract function invoke($apiPath, $data);
}
