<?php
/**
 * To handle everything regarding our main Admin Settings Page
 *
 * @author Wasseem Khayrattee <wasseemk@ringier.co.za>
 * @github wkhayrattee
 */

namespace B2Sync;

use Timber\FunctionWrapper;
use Timber\Timber;

class AdminSettingsPage
{
    public function __construct()
    {
    }

    /**
     * Main method for handling the admin pages
     */
    public function handleAdminUI()
    {
        $this->addAdminPages();

        // Register a new section in our page.
        add_settings_section(
            Enum::ADMIN_SETTINGS_SECTION_1,
            'Please fill in the below, mandatory are marked by an asterisk <span style="color:red;">*</span>',
            [self::class, 'settingsSectionCallback'],
            Enum::ADMIN_SETTINGS_MENU_SLUG
        );
    }

    public static function settingsSectionCallback($args)
    {
        //silence for now
    }

    public function addAdminPages()
    {
        //the settings main page
        add_menu_page(
            Enum::ADMIN_SETTINGS_PAGE_TITLE,
            Enum::ADMIN_SETTINGS_MENU_TITLE,
            'manage_options',
            Enum::ADMIN_SETTINGS_MENU_SLUG,
            [self::class, 'renderSettingsPage'],
            'dashicons-rest-api',
            20
        );

        //Fields for the Settings main-PAGE
        $this->addFieldsViaSettingsAPI();
    }

    /**
     * Handle & Render our Admin Settings Page
     */
    public static function renderSettingsPage()
    {
        global $title;
        $error_msg = '';

        if (!current_user_can('manage_options')) {
            return;
        }

        //SYNC ACTION BUTTON TRIGGERED
        if (isset($_POST['process_sync_btn'])) {
            $error_msg = Utils::doSync();
        }

        $timber = new Timber();
        $settings_page_tpl = B2Sync_PLUGIN_VIEWS . 'admin' . B2Sync_DS . 'page_settings.twig';

        if (file_exists($settings_page_tpl)) {
            $context['settings_page_nonce'] = wp_create_nonce('ajax-nonce');
            $context['admin_url'] = get_admin_url();
            $context['error_msg'] = $error_msg;
            $context['admin_page_title'] = $title;
            $context['settings_fields'] = new FunctionWrapper('settings_fields', [Enum::SETTINGS_OPTION_GROUP]);
            $context['do_settings_sections'] = new FunctionWrapper('do_settings_sections', [Enum::ADMIN_SETTINGS_MENU_SLUG]);
            $context['submit_button'] = new FunctionWrapper('submit_button', ['Save Settings']);

            $timber::render($settings_page_tpl, $context);
        }
        unset($timber);
    }

    public function addFieldsViaSettingsAPI()
    {
        $this->add_field_status();
        $this->add_field_keyid();
        $this->add_field_applicationid();
        $this->add_field_bucketname();
        $this->add_field_uploads_folder_name();
    }

    // FIELDS Related
    public function add_field_status()
    {
        add_settings_field(
            Enum::SLUG_PREFIX . Enum::FIELD_STATUS,
            // Use $args' label_for to populate the id inside the callback.
            'Enable B2-Sync<span style="color:red;">*</span>',
            [self::class, 'field_status_callback'],
            Enum::ADMIN_SETTINGS_MENU_SLUG,
            Enum::ADMIN_SETTINGS_SECTION_1,
            [
                'label_for' => Enum::FIELD_STATUS,
                'class' => 'b2-sync-row',
                'field_custom_data' => Enum::FIELD_STATUS,
            ]
        );
    }

    /**
     * field FIELD_B2SYNC_STATUS callback function.
     *
     * WordPress has magic interaction with the following keys: label_for, class.
     * - the "label_for" key value is used for the "for" attribute of the <label>.
     * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
     * Note: you can add custom key value pairs to be used inside your callbacks.
     *
     * @param array $args
     */
    public static function field_status_callback($args)
    {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option(Enum::SETTINGS_OPTION_NAME);

        $timber = new Timber();
        $field_status_tpl = B2Sync_PLUGIN_VIEWS . 'admin' . B2Sync_DS . 'field_status_dropdown.twig';

        $status_selected_on = $status_selected_off = '';
        if (isset($options[$args['label_for']])) {
            $status_selected_on = selected($options[ $args['label_for'] ], 'on', false);
            $status_selected_off = selected($options[ $args['label_for'] ], 'off', false);
        }

        if (file_exists($field_status_tpl)) {
            $context['field_status_name'] = Enum::SETTINGS_OPTION_NAME . '[' . esc_attr($args['label_for']) . ']';
            $context['label_for'] = esc_attr($args['label_for']);
            $context['field_custom_data'] = esc_attr($args['field_custom_data']);
            $context['field_custom_data_selected_on'] = esc_attr($status_selected_on);
            $context['field_custom_data_selected_off'] = esc_attr($status_selected_off);

            $timber::render($field_status_tpl, $context);
        }
        unset($timber);
    }

    //Key ID
    public function add_field_keyid()
    {
        add_settings_field(
            Enum::SLUG_PREFIX . Enum::FIELD_KEY_ID,
            // Use $args' label_for to populate the id inside the callback.
            'Key ID<span style="color:red;">*</span>',
            [self::class, 'field_keyid_callback'],
            Enum::ADMIN_SETTINGS_MENU_SLUG,
            Enum::ADMIN_SETTINGS_SECTION_1,
            [
                'label_for' => Enum::FIELD_KEY_ID,
                'class' => 'b2-sync-row',
                'field_custom_data' => Enum::FIELD_KEY_ID,
            ]
        );
    }

    public static function field_keyid_callback($args)
    {
        Utils::render_field_tpl($args, 'field_keyid.twig');
    }

    //Key Name
    public function add_field_applicationid()
    {
        add_settings_field(
            Enum::SLUG_PREFIX . Enum::FIELD_APPLICATION_KEY,
            // Use $args' label_for to populate the id inside the callback.
            'Application Key<span style="color:red;">*</span>',
            [self::class, 'field_applicationid_callback'],
            Enum::ADMIN_SETTINGS_MENU_SLUG,
            Enum::ADMIN_SETTINGS_SECTION_1,
            [
                'label_for' => Enum::FIELD_APPLICATION_KEY,
                'class' => 'b2-sync-row',
                'field_custom_data' => Enum::FIELD_APPLICATION_KEY,
            ]
        );
    }

    public static function field_applicationid_callback($args)
    {
        Utils::render_field_tpl($args, 'field_applicationid.twig');
    }

    //Bucket Name
    public function add_field_bucketname()
    {
        add_settings_field(
            Enum::SLUG_PREFIX . Enum::FIELD_BUCKET_NAME,
            // Use $args' label_for to populate the id inside the callback.
            'Bucket Name<span style="color:red;">*</span>',
            [self::class, 'field_bucketname_callback'],
            Enum::ADMIN_SETTINGS_MENU_SLUG,
            Enum::ADMIN_SETTINGS_SECTION_1,
            [
                'label_for' => Enum::FIELD_BUCKET_NAME,
                'class' => 'b2-sync-row',
                'field_custom_data' => Enum::FIELD_BUCKET_NAME,
            ]
        );
    }

    public static function field_bucketname_callback($args)
    {
        Utils::render_field_tpl($args, 'field_bucketname.twig');
    }

    //Bucket Name
    public function add_field_uploads_folder_name()
    {
        add_settings_field(
            Enum::SLUG_PREFIX . Enum::FIELD_UPLOADS_FOLDER_NAME,
            // Use $args' label_for to populate the id inside the callback.
            'Uploads folder name<span style="color:red;">*</span>',
            [self::class, 'field_uploads_folder_name_callback'],
            Enum::ADMIN_SETTINGS_MENU_SLUG,
            Enum::ADMIN_SETTINGS_SECTION_1,
            [
                'label_for' => Enum::FIELD_UPLOADS_FOLDER_NAME,
                'class' => 'b2-sync-row',
                'field_custom_data' => Enum::FIELD_UPLOADS_FOLDER_NAME,
            ]
        );
    }

    public static function field_uploads_folder_name_callback($args)
    {
        Utils::render_field_tpl($args, 'field_uploads_folder_name.twig');
    }
}
