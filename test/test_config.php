<?php


use ChikyuSdk\Config\Configs;

class TestConfig {
    private $ini;
    public function __construct() {
        $n = Configs::mode();
        $this->ini = parse_ini_file(__DIR__ . "/config.{$n}.ini", true);
    }

    public function item($section, $name) {
        return $this->ini[$section][$name];
    }
}
