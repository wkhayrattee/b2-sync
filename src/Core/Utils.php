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
}
