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
function B2Sync_infologthis($message)
{
    $log = new Logger('B2Sync_plugin_infolog');
    $stream = new StreamHandler(WP_CONTENT_DIR . B2Sync_DS . Enum::LOG_FILE_MESSAGE, Logger::INFO);
    $log->pushHandler($stream);
    $log->info($message);
    unset($log);
    unset($stream);
}
function B2Sync_errorlogthis($message)
{
    $log = new Logger('B2Sync_plugin_errorlog');
    $stream = new StreamHandler(WP_CONTENT_DIR . B2Sync_DS . Enum::LOG_FILE_ERROR, Logger::ERROR);
    $log->pushHandler($stream);
    $log->error($message);
    unset($log);
    unset($stream);
}
