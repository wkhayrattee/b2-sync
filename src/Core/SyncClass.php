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
    public $field_status = '';
    public $key_id = '';
    public $application_key = '';
    public $bucket_name = '';
    public $uploads_folder_name = '';

    public function __construct()
    {
        $this->initialiseVars();
        $this->validateFields();
    }

    private function initialiseVars()
    {
        $optionList = get_option(Enum::SETTINGS_OPTION_NAME);
        $this->field_status = Enum::FIELD_STATUS_VALUE_OFF;

        if (is_array($optionList) && (Utils::notEmptyOrNull($optionList))) {
            $this->field_status = $optionList[Enum::FIELD_STATUS];
        }

        if ($this->field_status == Enum::FIELD_STATUS_VALUE_ON) {
            if (isset($optionList[Enum::FIELD_KEY_ID])) {
                $this->key_id = $optionList[Enum::FIELD_KEY_ID];
            }
            if (isset($optionList[Enum::FIELD_APPLICATION_KEY])) {
                $this->application_key = $optionList[Enum::FIELD_APPLICATION_KEY];
            }
            if (isset($optionList[Enum::FIELD_BUCKET_NAME])) {
                $this->bucket_name = $optionList[Enum::FIELD_BUCKET_NAME];
            }
            if (isset($optionList[Enum::FIELD_UPLOADS_FOLDER_NAME])) {
                $this->uploads_folder_name = $optionList[Enum::FIELD_UPLOADS_FOLDER_NAME];
            }
        }
    }

    private function validateFields()
    {
        $error = '';
        if (!Utils::notEmptyOrNull($this->key_id)) {
            $error .= 'key_id empty ||';
            B2Sync_errorlogthis('[fields] KeyID cannot be empty!');
        }
        if (!Utils::notEmptyOrNull($this->application_key)) {
            $error .= 'application_key empty ||';
            B2Sync_errorlogthis('[fields] ApplicationKey cannot be empty!');
        }
        if (!Utils::notEmptyOrNull($this->bucket_name)) {
            $error .= 'bucket_name empty ||';
            B2Sync_errorlogthis('[fields] BucketName cannot be empty!');
        }

        if (mb_strlen($error) > 0) {
            $this->field_status = Enum::FIELD_STATUS_VALUE_OFF;
            B2Sync_errorlogthis('[WARNING] setting b2-sync to OFF - by rule, as some field(s) mentioned above is empty');
        }
    }

    public static function checkRclone()
    {
        /**
         * Find if rclone is on the machine
         */
        $executableFinder = new ExecutableFinder();
        $rclonePath = $executableFinder->find('rclone', Enum::NOT_FOUND, ['/usr/local/bin', '/usr/bin']);

        if ($rclonePath == Enum::NOT_FOUND) {
            B2Sync_errorlogthis('RCLONE was not found on this server');

            return false;
        }

        return true;
    }

    /**
     * We are leverage symfony/process to run the rclone asynchronously
     * ref: https://symfony.com/doc/current/components/process.html#running-processes-asynchronously
     *
     * @throws \Exception
     */
    public function start()
    {
        if (($this->field_status == Enum::FIELD_STATUS_VALUE_ON) && (self::checkRclone() === true)) {
            // The normal standard place where the WordPress uploads folder resides
            $path_to_uploads = WP_CONTENT_DIR . B2Sync_DS . 'uploads';

            /*
             * We are building the following path example:
             *  '/path/to/wp-content/uploads :b2,account="keyID",key="applicationKey":yourbucketname/uploads'
             */
            $remote_path = ':b2,account="' . $this->key_id . '",key="' . $this->application_key . '":' . $this->bucket_name . '/' . $this->uploads_folder_name;

            B2Sync_errorlogthis('Has started the syncing process..');
            /*
             * Command we are trying to execute on Bash:
             * $ rclone -q sync /path/to/wp-content/uploads :b2,account="keyID",key="applicationKey":yourbucketname/uploads
             */
            $process = new Process([
                'rclone',
                '-q',
                'sync',
                $path_to_uploads,
                $remote_path,
            ]);
            $process->start();

//            while ($process->isRunning()) {
//                //waiting for process to finish
//            }

            if ($process->isRunning() == false) {
                B2Sync_errorlogthis('Syncing done!');
            }

            if ($process->isSuccessful()) {
                $output = $process->getOutput();
                B2Sync_errorlogthis('Syncing seems to be successful!');
                B2Sync_errorlogthis($output);
            } else {
                B2Sync_errorlogthis('There seems to be an issue, see output below');
                B2Sync_errorlogthis($process->getErrorOutput());
            }
        }
    }
}
