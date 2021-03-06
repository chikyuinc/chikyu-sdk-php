<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Chikyu\Sdk\Config\ApiConfig;

date_default_timezone_set('Asia/Tokyo');


class TestConfig {
    private $ini;
    public function __construct($mode) {
        ApiConfig::setMode($mode);
        $n = ApiConfig::mode();
        $this->ini = parse_ini_file(__DIR__ . "/Config.{$n}.ini", true);
    }

    public function item($section, $name) {
        return $this->ini[$section][$name];
    }
}
