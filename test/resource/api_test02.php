<?php

use Chikyu\Sdk\Resource\Session;
use Chikyu\Sdk\Resource\Token;

require_once "../test_config.php";

$config = new TestConfig('local');

$token = new Token();
$token_name = 'hogehogepiyo';

$t = $token->create(
    $token_name,
    $config->item('login', 'email'),
    $config->item('login', 'password'),
    86400 * 30 * 12 * 10
);
print_r($t);

$r = $token->renew(
    $token_name,
    $t['login_token'],
    $t['login_secret_token']
);
print_r($r);

$session = Session::login($token_name, $r['login_token'], $r['login_secret_token']);

$r = $token->revoke(
    $token_name,
    $r['login_token'],
    $r['login_secret_token'],
    $session
);
print_r($r);

$session->logout();
