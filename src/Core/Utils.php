<?php
/**
 * Reusable methods
 *
 * @author Wasseem Khayrattee <wasseemk@ringier.co.za>
 * @github wkhayrattee
 */

namespace B2Sync;

use Timber\Timber;

class Utils
{
    /**
     * @param $args
     * @param $tpl_name
     */
    public static function render_field_tpl($args, $tpl_name): void
    {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option(Enum::SETTINGS_OPTION_NAME);

        $timber = new Timber();
        $field_tpl = B2Sync_PLUGIN_VIEWS . 'admin' . B2Sync_DS . $tpl_name;

        $field_value = '';
        if (isset($options[$args['label_for']])) {
            $field_value = $options[$args['label_for']];
        }

        if (file_exists($field_tpl)) {
            $context['field_name'] = Enum::SETTINGS_OPTION_NAME . '[' . esc_attr($args['label_for']) . ']';
            $context['label_for'] = esc_attr($args['label_for']);
            $context['field_custom_data'] = esc_attr($args['field_custom_data']);
            $context['field_value'] = esc_attr($field_value);

            $timber::render($field_tpl, $context);
        }
        unset($timber);
    }

    /**
     * Checks whether the value is not empty or not null
     *
     * @param $value
     *
     * @return bool
     */
    public static function notEmptyOrNull($value)
    {
        if (is_object($value) && !is_null($value)) {
            return true;
        }
        if (is_array($value)) {
            if (count($value) == 1) { //to cope with [''] and [' '] arrays
                if (self::isAssociative($value)) {
                    return true;
                } elseif (isset($value[0]) && self::notEmptyOrNull($value[0])) {
                    return true;
                }

                return false;
            }
            if (sizeof($value) > 0) {
                return true;
            }

            return true;
        } else {
            if ((is_string($value) || is_int($value)) && ($value != '') && ($value != 'NULL') && (mb_strlen(trim($value)) > 0)) {
                return true;
            }

            return false;
        }
    }

    /**
     * To verify if an array is associative
     *
     * @param $thatArray
     *
     * @return bool
     */
    public static function isAssociative($thatArray)
    {
        foreach ($thatArray as $key => $value) {
            if ($key !== (int) $key) {
                return true;
            }
        }

        return false;
    }

    public static function doSync($action = 'action_button')
    {
        $error_msg = '';

        /**
         * To check is there's any currently running rclone process
         */
        $is_running = SyncClass::checkAnyCurrentRunningProcess();
        if ($is_running === true) {
            B2Sync_logthis('[WARNING] You have invoked the sync more than once, Allow some time to let the current syncing complete!');

            return false;
        } else {
            if (SyncClass::checkRclone() === false) {
                $error_msg = '[ERROR] the software "rclone" does not seem to be present on your server, please ask your server admin to install it before using this plugin';
            } else {
                //Clear log file before starting next sync
                $error_log_file = WP_CONTENT_DIR . B2Sync_DS . Enum::LOG_FILE_ERROR;
                AdminLogPage::clearErrorLog($error_log_file);

                B2Sync_logthis('[INFO] A Sync was triggered by action: ' . $action);

                $sync = new SyncClass();
                if ($sync->field_status == Enum::FIELD_STATUS_VALUE_OFF) {
                    $error_msg = '[ERROR] Please enable the sync below in the dropdown or one of the field(s) is empty!';

                    B2Sync_logthis('[ERROR] ' . $error_msg);
                } else {
                    $error_msg = '[INFO] Syncing has started, check the log on the sub-menu ' . Enum::ADMIN_LOG_MENU_TITLE . ' page';

                    $sync->start();
                }
            }

            return $error_msg;
        }
    }

    /**
     * This is use to schedule and unschedule our sync event
     *
     * @param mixed $do_remove_schedule_only
     */
    public static function handle_state_of_schedule_task($do_remove_schedule_only = false)
    {
        $timestampNow = date_timestamp_get(date_create()); //get a UNIX Timestamp for NOW
        /*
         * We use WordPress Time Constants
         * https://codex.wordpress.org/Easier_Expression_of_Time_Constants
         */
        $currentTimestampForAction = $timestampNow + (int) Enum::SECONDS_AFTER_TO_RUN;
        $args = ['rest_action_b2sync'];
        $hookB2SyncAction = Enum::HOOK_DO_SYNC;

        /*
         * timestmap of any already scheduled event with SAME args
         *      - needs to be uniquely identified will return false if not scheduled
         */
        $alreadyScheduledTimestamp = wp_next_scheduled($hookB2SyncAction, $args);

        if ($do_remove_schedule_only === false) {
            if ($alreadyScheduledTimestamp === false) { //means first time this cron is being scheduled
                wp_schedule_single_event($currentTimestampForAction, $hookB2SyncAction, $args, true);
            } else { //is not on first time
                //unschedule current
                wp_unschedule_event($alreadyScheduledTimestamp, $hookB2SyncAction, $args);
                //we want to remove any pre existing ones

                //re-schedule same for another time
                wp_schedule_single_event($currentTimestampForAction, $hookB2SyncAction, $args, true);
            }
        } else { //this is for deactivation hook
            wp_unschedule_event($alreadyScheduledTimestamp, $hookB2SyncAction, $args);
        }
    }
}
