<?php
require_once __DIR__ . "/../test_config.php";

use Chikyu\Sdk\Error\ApiExecuteException;
use Monolog\Logger;
use Chikyu\Sdk\Log\ApiLogger;

# ApiLogger::init(null, null, Logger::INFO);

ApiLogger::debug('test');
ApiLogger::info('test');
ApiLogger::warn('test');
try {
    throw new ApiExecuteException('テストです');
} catch(ApiExecuteException $e) {
    ApiLogger::error('test', $e);
}

