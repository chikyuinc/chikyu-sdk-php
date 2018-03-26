<?php namespace Chikyu\Sdk\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class ApiLogger {
    private static $infoPath = 'php://stdout';
    private static $errorPath = 'php://stderr';
    private static $level = Logger::INFO;
    private static $logger = null;

    public static function init($infoPath=null, $errorPath=null, $level=null) {
       if ($infoPath) {
           self::$infoPath = $infoPath;
       } else {
           self::$infoPath = 'php://stdout';
       }

        if ($errorPath) {
            self::$errorPath = $errorPath;
        } else {
            self::$errorPath = 'php://stderr';
        }

        self::$logger = null;
        if ($level) {
            self::$level = $level;
        } else {
            self::$level = Logger::INFO;
        }
    }

    public static function getLogger() {
        if (!self::$infoPath && !self::$errorPath) {
            return null;
        }

        if (self::$logger == null) {
            self::$logger = new Logger('chikyu.sdk');
            try {
                if (self::$infoPath) {
                    self::$logger->pushHandler(new StreamHandler(self::$infoPath, self::$level));
                }

                $errLevel = null;
                if (self::$level <= Logger::NOTICE) {
                    $errLevel = Logger::WARNING;
                } else {
                    $errLevel = self::$level;
                }

                if (self::$errorPath) {
                    self::$logger->pushHandler(new StreamHandler(self::$errorPath, $errLevel));
                }
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }

        return self::$logger;
    }

    public static function isDebug() {
        $logger = self::getLogger();
        if ($logger) {
            return $logger->isHandling(Logger::DEBUG);
        } else {
            return false;
        }
    }

    public static function isInfo() {
        $logger = self::getLogger();
        if ($logger) {
            return $logger->isHandling(Logger::INFO);
        } else {
            return false;
        }
    }

    public static function isWarn() {
        $logger = self::getLogger();
        if ($logger) {
            return $logger->isHandling(Logger::WARNING);
        } else {
            return false;
        }
    }

    public static function isError() {
        $logger = self::getLogger();
        if ($logger) {
            return $logger->isHandling(Logger::ERROR);
        } else {
            return false;
        }
    }

    public static function debug($message) {
        $logger = self::getLogger();
        if ($logger) {
            $logger->debug(self::parseMessage($message));
        }
    }

    public static function info($message) {
        $logger = self::getLogger();
        if ($logger) {
            $logger->info(self::parseMessage($message));
        }
    }

    public static function warn($message, \Exception $exception = null) {
        $logger = self::getLogger();
        if ($logger) {
            if ($exception) {
                $ctx = ['exception' => $exception];
                $logger->warn(self::parseMessage($message), $ctx);
            } else {
                $logger->warn(self::parseMessage($message));
            }
        }
    }

    public static function error($message, \Exception $exception = null) {
        $logger = self::getLogger();
        if ($logger) {
            if ($exception) {
                $ctx = ['exception' => $exception];
                $logger->error(self::parseMessage($message), $ctx);
            } else {
                $logger->error(self::parseMessage($message));
            }
        }
    }

    private static function parseMessage($message) {
        if (is_string($message)) {
            return $message;
        } else {
            return json_encode($message);
        }
    }
}
