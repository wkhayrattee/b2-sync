<?php
/**
 * A some handy functions to use directly without namespace
 *
 * @author Wasseem Khayrattee <hey@wk.contact>
 * @github wkhayrattee
 */
use B2Sync\Enum;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Wrapper to log Messages in a custom log file
 *
 * @param $message
 * @param mixed $isError
 *
 * @throws \Exception
 */
function B2Sync_logthis($message)
{
    $log = new Logger('B2Sync_LOG');
    $stream = new StreamHandler(WP_CONTENT_DIR . B2Sync_DS . Enum::LOG_FILE_ERROR, Logger::INFO);
    $log->pushHandler($stream);
    $log->info($message);
    unset($log);
    unset($stream);
}
