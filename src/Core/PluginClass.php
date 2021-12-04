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
        // Remove any scheduled cron jobs.
        Utils::handle_state_of_schedule_task(true);
    }

    /**
     * triggered when a user has deactivated the plugin
     */
    public static function plugin_uninstall()
    {
        delete_option(Enum::SETTINGS_OPTION_NAME);
        delete_option(Enum::PLUGIN_KEY);
        // Remove any scheduled cron jobs.
        Utils::handle_state_of_schedule_task(true);
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

        //register schedule event - 15secs
        add_action(Enum::HOOK_DO_SYNC, [self::class, 'cronExecuteHook'], 10, 1);

        // register the ajax action for authenticated users
        add_action('wp_ajax_ajax_method_to_trigger_sync', [self::class, 'ajax_method_to_trigger_sync']);
        // register the ajax action for unauthenticated users
        add_action('wp_ajax_nopriv_ajax_method_to_trigger_sync', [self::class, 'ajax_method_to_trigger_sync']);
    }

    public static function register_sync_actions()
    {
        $sync_on_actions_list = [
            'publish_phone',
            'save_post',
            'edit_post',
        ];

        foreach ($sync_on_actions_list as $action) {
            if (did_action($action)) {
                self::schedule_sync_once($action);
            } else {
                add_action($action, [self::class, 'schedule_sync_once']);
            }
        }
    }

    /**
     * To schedule the task fo sync for later - after 15secs
     *
     * @param $action
     */
    public static function schedule_sync_once($action)
    {
        $error_msg = '';
        static $completed = false;
        if (!$completed) {
            $completed = true;
            Utils::handle_state_of_schedule_task();
        }
    }

    /**
     * Will be called by the scheduled cron
     */
    public static function cronExecuteHook()
    {
        $error_msg = Utils::doSync('action_via_rest_api');
    }

    /**
     * This is to register our AJAX method for the Manual Sync Button on the admin page
     */
    public static function ajax_method_to_trigger_sync()
    {
        // Check for nonce security
        if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
            B2Sync_logthis('[ERROR] ajax nonce failed while sync button was clicked');
            wp_send_json_error('An error occurred');
        } else {
            $error_msg = Utils::doSync('manual_ajax_action');
            wp_send_json_success('Sync process completed.');
        }

        // required. to end AJAX request
        wp_die();
    }
}
