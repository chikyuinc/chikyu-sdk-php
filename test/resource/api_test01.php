<?php

use ChikyuSdk\Resource\Session;
use ChikyuSdk\SecureResource;

require_once '../../vendor/autoload.php';
require_once "../../init.php";
require_once "../test_config.php";

$config = new TestConfig();

$session = Session::login(
    $config->item('token', 'token_name'),
    $config->item('token', 'login_token'),
    $config->item('token', 'login_secret_token')
);

$session->changeOrgan(1460);

$resource = new SecureResource($session);

$data = array(
    'items_per_page' => 10,
    'page_index' => 0
);

$r = $resource->invoke('/session/organ/list', $data);

print_r($r);

$session->logout();
