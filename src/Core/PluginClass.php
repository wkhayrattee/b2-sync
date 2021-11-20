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
        add_option(Enum::PLUGIN_KEY, true);

        //info: 'Activation: set plugin_key to true'
        B2Sync_logthis('Activation: set plugin_key to true', false);

        //info: 'plugin_activated'
        B2Sync_logthis('plugin_activated', false);
    }

    /**
     * Should remove any scheduled events
     * NOTE: The database data cleaning is handled by uninstall.php
     *
     * @static
     */
    public static function plugin_deactivation()
    {
        //info: 'plugin_deactivated'
        B2Sync_logthis('plugin_deactivated', false);

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
        delete_option(Enum::PLUGIN_KEY);

        //info: 'Uninstallation: flushed plugin_key'
        B2Sync_logthis('Uninstallation: flushed plugin_key', false);

        //info: 'plugin_uninstall hook called'
        B2Sync_logthis('plugin_uninstall hook called', false);
    }
}
