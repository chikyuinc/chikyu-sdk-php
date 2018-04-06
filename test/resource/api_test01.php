<?php
require_once __DIR__ . "/../test_config.php";

use Chikyu\Sdk\Log\ApiLogger;
use Chikyu\Sdk\Resource\Session;
use Chikyu\Sdk\SecureResource;
use Monolog\Logger;

ApiLogger::init(null, null, Logger::DEBUG);

$config = new TestConfig('local');

$session = Session::login(
    $config->item('token', 'token_name'),
    $config->item('token', 'login_token'),
    $config->item('token', 'login_secret_token')
);

$s = strval($session);
$s2 = Session::fromStr($s);

$s2->changeOrgan(1460);


$resource = new SecureResource($s2);

$data = array(
    'items_per_page' => 10,
    'page_index' => 0
);

$r = $resource->invoke('/session/organ/list', $data);

print_r($r);

$s2->logout();
