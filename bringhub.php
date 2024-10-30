<?php
/*
Plugin Name: Bringhub
Description: Bringhub's fully automated product suite helps bloggers and publishers generate more affiliate revenue by improving affiliate CTR.  We help you make money while you sleep.  Activate plugin to get started.
Version:     2018.03.19
Author:      Bringhub
Author URI:  http://www.bringhub.com
License:     GPL2
 */

/*
Bringhub is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Bringhub is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Bringhub. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.
 */

#define plugin location
define('BRINGHUB_DIR', plugin_dir_path(__FILE__));
define('BRINGHUB_URL', plugin_dir_url(__FILE__));
define('BRINGHUB_VERSION', '2018.03.19');

#Generic functions
include_once 'includes/functions.php';
include_once 'includes/settings.php';
#main shortcode
include_once 'shortcodes/script.php';

$bringhub = new bringhub;

class bringhub
{

    #initiate the filters
    public function __construct()
    {
        add_filter('the_content', array($this, 'the_content_after'), 99);
        add_action('admin_init', array($this, 'shortcode_button_init'));
        add_filter('post_class', array($this, 'add_bh_class' ));
    }

    function add_bh_class( $classes ) {
        $classes[] = 'bh-container';
        return $classes;
    }

    public function the_content_after($content)
    {
        global $post, $bringhub_settings;

        if (
            strpos($post->post_content, '[bringhub') === false &&
            strpos($post->post_content, '[disable_bringhub]') === false &&
            supports_bringhub($post->post_type) &&
            is_single()
        ) {
            $content .= bringhub_get_template('script.php');
        }

        if (
            strpos($post->post_content, '[disable_bringhub]') !== false
        ) {
            $content = str_replace('[disable_bringhub]', '', $content);
        }
        return $content;
    }

    #add shortcode button
    public function shortcode_button_init()
    {

        #Abort early if the user will never see TinyMCE
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true') {
            return;
        }

        #Add a callback to regiser our tinymce plugin
        add_filter("mce_external_plugins", array($this, "register_tinymce_plugin"));

        #Add a callback to add our button to the TinyMCE toolbar
        add_filter('mce_buttons', array($this, 'add_tinymce_button'));
    }

    #This callback registers our plug-in
    public function register_tinymce_plugin($plugin_array)
    {
        $plugin_array['bringhub_button'] = plugins_url('assets/js/admin.js?v2', __FILE__);
        $plugin_array['disable_bringhub_button'] = plugins_url('assets/js/admin.js?v2', __FILE__);

        return $plugin_array;
    }

    #This callback adds our button to the toolbar
    public function add_tinymce_button($buttons)
    {
        #Add the button ID to the $button array
        $buttons[] = "bringhub_button";
        $buttons[] = "disable_bringhub_button";
        return $buttons;
    }
}
