<?php namespace ChikyuSdk;

use ChikyuSdk\Error\ApiExecuteException;

class OpenResource extends ApiResource {

    /**
     * @param $apiPath
     * @param $data
     * @return mixed
     * @throws ApiExecuteException
     */
    function invoke($apiPath, $data) {
        $url = self::buildUrl('open', $apiPath);
        $res = self::sendRequest($url, array('data' => $data), array());
        return self::handleResponse($res);
    }
}
