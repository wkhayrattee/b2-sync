<?php
/**
 *  Handles the Main plugin hooks
 *
 * @author Wasseem Khayrattee <hey@wk.contact>
 * @github wkhayrattee
 */

namespace B2Sync;

class PluginClass
{
    /**
     * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
     *
     * @static
     */
    public static function plugin_activation()
    {
        //added to handle post_activation stuffs as the plugin does not have notion of this state
        add_option(Enum::PLUGIN_KEY, 'ON');
    }

    /**
     * Should remove any scheduled events
     * NOTE: The database data cleaning is handled by uninstall.php
     *
     * @static
     */
    public static function plugin_deactivation()
    {
        // TODO: Remove any scheduled cron jobs.
//        $my_cron_events = array(
//            'my_schedule_cron_recheck', //todo: use our Enum for this (wasseem)
//            'my_scheduled_delete',
//        );
//
//        foreach ( $my_cron_events as $current_cron_event ) {
//            $timestamp = wp_next_scheduled( $current_cron_event );
//
//            if ( $timestamp ) {
//                wp_unschedule_event( $timestamp, $current_cron_event );
//            }
//        }
    }

    /**
     * triggered when a user has deactivated the plugin
     */
    public static function plugin_uninstall()
    {
        delete_option(Enum::SETTINGS_OPTION_NAME);
        delete_option(Enum::PLUGIN_KEY);
    }

    public static function adminInit()
    {
        //if on plugin activation
        if (get_option(Enum::PLUGIN_KEY)) {
            delete_option(Enum::PLUGIN_KEY);

            //initially if there is anything we need to initialise
            update_option(
                Enum::SETTINGS_OPTION_NAME,
                [
                    Enum::FIELD_STATUS => Enum::FIELD_STATUS_VALUE_OFF,
                    Enum::FIELD_UPLOADS_FOLDER_NAME => 'uploads',
                ]
            );
        }
        //Now do normal stuff
        add_action('admin_menu', [self::class, 'handleAdminUI']);
    }

    public static function handleAdminUI()
    {
        // Register a setting to group the data for our plugin
        register_setting(Enum::SETTINGS_OPTION_GROUP, Enum::SETTINGS_OPTION_NAME);

        //The "admin Settings" main-PAGE
        $adminSettingsPage = new AdminSettingsPage();
        $adminSettingsPage->handleAdminUI();

        //The "Log" sub-PAGE
        $adminLogPage = new AdminLogPage();
        $adminLogPage->handleAdminUI();
    }

    public static function plugin_start()
    {
        add_action('init', [self::class, 'register_sync_actions'], 20);
    }

    public static function register_sync_actions()
    {
        $purge_actions = [
            'publish_phone',
            'save_post',
            'edit_post',
        ];

        foreach ($purge_actions as $action) {
            if (did_action($action)) {
                self::do_sync_once();
            } else {
                add_action($action, [self::class, 'do_sync_once']);
            }
        }
    }

    public static function do_sync_once()
    {
        static $completed = false;
        if (!$completed) {
            Utils::doSync();
            $completed = true;
        }
    }
}
