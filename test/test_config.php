<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Chikyu\Sdk\Config\Configs;

class TestConfig {
    private $ini;
    public function __construct() {
        $n = Configs::mode();
        $this->ini = parse_ini_file(__DIR__ . "/Config.{$n}.ini", true);
    }

    public function item($section, $name) {
        return $this->ini[$section][$name];
    }
}
