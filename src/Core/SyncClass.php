<?php
/**
 * The main class to handle the logic for sync-ing the folder onto the backblaze b2 bucket
 *
 * @author Wasseem Khayrattee <wasseemk@ringier.co.za>
 * @github wkhayrattee
 */

namespace B2Sync;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class SyncClass
{
    public function __construct()
    {
    }

    public static function start()
    {
        $key_id = '';
        $key_name = '';
        $bucket_name = '';
        $uploads_folder_name = '';

        /**
         * Find if rclone is on the machine
         */
        $executableFinder = new ExecutableFinder();
        $rclonePath = $executableFinder->find('rclone', Enum::NOT_FOUND, ['/usr/local/bin', '/usr/bin']);

        if ($rclonePath == Enum::NOT_FOUND) {
            B2Sync_errorlogthis('RCLONE was not found on this server');

            return false;
        }

        B2Sync_infologthis('process started..');
        $process = new Process([
            'rclone',
            '-q',
            'sync',
            '/var/www/projects/local/wordpress-with-composer/public/wp-content/test-folder/',
            ':b2,account="xxx0004287487d6d360000000005",key="xxxK000uj9XXHNTd/5DmV0o/K9B2WDthrg":innerfolderna,e/test-folder',
        ]);
        $process->start();

        while ($process->isRunning()) {
            //waiting for process to finish
        }

        $output = $process->getOutput();

        if ($process->isSuccessful()) {
            B2Sync_infologthis('Syncing seems to be successful!');
        } else {
            B2Sync_errorlogthis('There seems to be an issue, see output below');
            B2Sync_errorlogthis($process->getErrorOutput());
        }
    }
}
