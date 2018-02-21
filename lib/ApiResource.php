<?php namespace Chikyu\Sdk;

use Chikyu\Sdk\Config\Configs;
use Chikyu\Sdk\Error\ApiExecuteException;

abstract class ApiResource {
    static function buildUrl($apiClass, $apiPath, $withHost=true) {
        $p = Configs::protocol();
        $h = Configs::host();
        $e = Configs::envName();

        if (strpos($apiPath, '/') === 0) {
            $apiPath = substr($apiPath, 1);
        }

        if ($withHost) {
            if ($e) {
                $e = "/{$e}";
            }

            return "{$p}://{$h}{$e}/api/v2/{$apiClass}/{$apiPath}";
        } else {
            return "/{$e}/api/v2/{$apiClass}/{$apiPath}";
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

        //print_r($header_list);
        print($url . "\n");
        $result = file_get_contents($url, false, stream_context_create(array( 'http' =>
            array(
                'method' => 'POST',
                'header' => implode(PHP_EOL, $header_list),
                'content' => json_encode($data),
                'ignore_errors' => false
            )
        )));

        //print_r($http_response_header);

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

        return $map['data'];
    }

    abstract function invoke($apiPath, $data);
}
