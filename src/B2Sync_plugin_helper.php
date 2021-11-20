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
function B2Sync_logthis($message, $isError = true)
{
    $log = new Logger('B2Sync_plugin_info_log');
    $stream = null;
    if ($isError === true) {
        $stream = new StreamHandler(WP_CONTENT_DIR . B2Sync_DS . Enum::LOG_FILE_ERROR, Logger::ERROR);
    } else {
        $stream = new StreamHandler(WP_CONTENT_DIR . B2Sync_DS . Enum::LOG_FILE_MESSAGE, Logger::INFO);
    }
    $log->pushHandler($stream);
    $log->info($message);
    unset($log);
    unset($stream);
}
