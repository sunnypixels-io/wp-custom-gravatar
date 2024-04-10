<?php
/**
 * Plugin Name: Custom Gravatar
 * Description: Add custom gravatar field to user profile.
 * Version: 1.0.0
 * Plugin URI: https://github.com/sunnypixels-io/wp-custom-gravatar
 * Author: Alex Zappa at SunnyPixels
 * Author URI: https://alex.zappa.dev/
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: sp-custom-gravatar
 */

if (!defined('ABSPATH'))
    exit;

if (class_exists('ACF')) {

    add_action('acf/include_fields', function () {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group(array(
            'key' => 'user_extra_options',
            'title' => __('User Extra Options', 'sp-custom-gravatar'),
            'fields' => array(
                array(
                    'key' => 'user_extra_options__custom_gravatar',
                    'label' => __('Custom Gravatar', 'sp-custom-gravatar'),
                    'name' => 'custom_gravatar',
                    'aria-label' => '',
                    'type' => 'image',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'return_format' => 'id',
                    'library' => 'all',
                    'min_width' => '',
                    'min_height' => '',
                    'min_size' => '',
                    'max_width' => '',
                    'max_height' => '',
                    'max_size' => '',
                    'mime_types' => '',
                    'preview_size' => 'thumbnail',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'user_form',
                        'operator' => '==',
                        'value' => 'all',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ));
    });

    function sunnypixels_custom_avatar($avatar, $id_or_email, $size, $default, $alt, $args)
    {
        $id = sunnypixels_get_user_id($id_or_email);

        if (!$id)
            return $avatar;

        $custom_avatar = get_user_meta($id, 'custom_gravatar', true);

        if ($custom_avatar) {
            $custom_avatar = wp_get_attachment_image_src($custom_avatar)[0];
            $avatar = "<img alt='{$alt}' src='{$custom_avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' {$args['extra_attr']} />";
        }

        return $avatar;
    }

    function sunnypixels_custom_avatar_url($url, $id_or_email, $args)
    {
        $id = sunnypixels_get_user_id($id_or_email);

        if (!$id)
            return $url;

        $custom_avatar = get_user_meta($id, 'custom_gravatar', true);
        if ($custom_avatar) {
            $custom_avatar = wp_get_attachment_image_src($custom_avatar)[0];
            $url = $custom_avatar;
        }
        return $url;
    }

    function sunnypixels_get_user_id($id_or_email)
    {
        if (is_numeric($id_or_email))
            return $id_or_email;

        if (is_object($id_or_email))
            return $id_or_email->user_id;

        if (is_string($id_or_email))
            return get_user_by('email', $id_or_email) ? get_user_by('email', $id_or_email)->ID : 0;

        return 0;
    }

    add_filter('get_avatar', 'sunnypixels_custom_avatar', 100500, 6);
    add_filter('get_avatar_url', 'sunnypixels_custom_avatar_url', 100500, 3);

} else {

    add_action('admin_notices', function () {
        printf(
            '<div class="notice notice-warning is-dismissible"><p>%s.</p></div>',
            __('Advanced Custom Fields plugin is required for Custom Gravatar functionality.', 'sp-custom-gravatar')
        );
    });

}
