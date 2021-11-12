<?php
/**
 * A some handy functions to use directly without namespace
 *
 * @author Wasseem Khayrattee <hey@wk.contact>
 * @github wkhayrattee
 */
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use WKWPB2\Enum;

/**
 * Wrapper to log Messages in a custom log file
 *
 * @param $message
 * @param mixed $isError
 *
 * @throws \Exception
 */
function wkwpb2_logthis($message, $isError = true)
{
    $log = new Logger('wkwpb2_plugin_info_log');
    $stream = null;
    if ($isError === true) {
        $stream = new StreamHandler(WP_CONTENT_DIR . DS . Enum::LOG_FILE_ERROR, Logger::ERROR);
    } else {
        $stream = new StreamHandler(WP_CONTENT_DIR . DS . Enum::LOG_FILE_MESSAGE, Logger::INFO);
    }
    $log->pushHandler($stream);
    $log->info($message);
    unset($log);
    unset($stream);
}
