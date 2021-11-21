<?php
/**
 *  A fake Enumeration class to keep things centralised
 *
 * @author Wasseem Khayrattee <hey@wk.contact>
 * @github wkhayrattee
 */

namespace B2Sync;

class Enum
{
    const PLUGIN_KEY = 'B2Sync_Plugin';
    const SLUG_PREFIX = 'b2_sync_';

    //Log files
    const LOG_FILE_MESSAGE = 'B2Sync_plugin.log';
    const LOG_FILE_ERROR = 'B2Sync_plugin_error.log';

    //Admin pages
    const SETTINGS_OPTION_GROUP = 'b2_sync_plugin_option_group';
    const SETTINGS_OPTION_NAME = 'b2_sync_plugin_option_name';

    const ADMIN_SETTINGS_PAGE_TITLE = 'B2-Sync Settings';
    const ADMIN_SETTINGS_MENU_TITLE = 'B2-Sync';
    const ADMIN_SETTINGS_MENU_SLUG = 'b2-sync-settings-page';
    const ADMIN_SETTINGS_SECTION_1 = 'b2-sync-settings-section01';

    const ADMIN_LOG_PAGE_TITLE = 'B2-Sync Log';
    const ADMIN_LOG_MENU_TITLE = 'B2-Sync LOG';
    const ADMIN_LOG_MENU_SLUG = 'b2-sync-log-page';
    const ADMIN_LOG_SECTION_1 = 'b2-sync-log-section01';

    //FIELDS
    const FIELD_STATUS_VALUE_ON = 'on';
    const FIELD_STATUS_VALUE_OFF = 'off';
    const FIELD_STATUS = 'field_status';
    const FIELD_KEY_ID = 'field_keyid';
    const FIELD_APPLICATION_KEY = 'field_applicationkey';
    const FIELD_BUCKET_NAME = 'field_bucketname';
    const FIELD_UPLOADS_FOLDER_NAME = 'field_uploads_folder_name';

    //Misc
    const NOT_FOUND = 'NOT_FOUND';
    const HOOK_DO_SYNC = 'hook_b2sync_plugin_process';
    const SECONDS_AFTER_TO_RUN = '15';
}
